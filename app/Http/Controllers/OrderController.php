<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Apply middleware for authentication
     */
    public function __construct()
    {

    }

    /**
     * Display a listing of user's orders
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        
        $orders = Order::forUser($user->user_id)
            ->with(['orderItems.product', 'orderItems.customProduct'])
            ->orderBy('order_date', 'desc')
            ->paginate(10);

        return view('orders.index', [
            'orders' => $orders,
        ]);
    }

    /**
     * Display the specified order
     */
    public function show($orderId): View
    {
        $user = auth()->user();
        
        $order = Order::forUser($user->user_id)
            ->with(['orderItems.product', 'orderItems.customProduct'])
            ->findOrFail($orderId);

        return view('orders.show', [
            'order' => $order,
            'orderDetails' => $order->getFullDetails(),
        ]);
    }

    /**
     * Show order tracking page
     */
    public function tracking($orderId): View
    {
        $user = auth()->user();
        
        $order = Order::forUser($user->user_id)->findOrFail($orderId);

        $trackingSteps = [
            'processing' => [
                'status' => 'processing',
                'title' => 'Order Processing',
                'description' => 'Your order is being prepared',
                'completed' => true,
                'date' => $order->order_date
            ],
            'shipped' => [
                'status' => 'shipped',
                'title' => 'Order Shipped',
                'description' => 'Your order has been shipped',
                'completed' => in_array($order->shipping_status, ['shipped', 'delivered']),
                'date' => null
            ],
            'delivered' => [
                'status' => 'delivered',
                'title' => 'Order Delivered',
                'description' => 'Your order has been delivered',
                'completed' => $order->shipping_status === 'delivered',
                'date' => null
            ]
        ];

        return view('orders.tracking', [
            'order' => $order,
            'trackingSteps' => $trackingSteps,
        ]);
    }

    /**
     * Cancel an order
     */
    public function cancel($orderId): RedirectResponse
    {
        try {
            $user = auth()->user();
            
            $order = Order::forUser($user->user_id)->findOrFail($orderId);

            if (!$order->canBeCancelled()) {
                return back()->with('error', 'Order cannot be cancelled in its current status.');
            }

            $order->updateOrder(['shipping_status' => 'cancelled']);

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order cancelled successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to cancel order. Please try again.');
        }
    }

    /**
     * Admin Routes
     */

    /**
     * Display all orders for admin
     */
    public function adminIndex(Request $request): View
    {
        $query = Order::with(['user:user_id,first_name,last_name,email', 'orderItems']);

        // Apply filters
        if ($request->status) {
            $query->byShippingStatus($request->status);
        }

        if ($request->payment_status) {
            $query->byPaymentStatus($request->payment_status);
        }

        if ($request->search) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('email', 'like', "%{$request->search}%")
                  ->orWhere('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name', 'like', "%{$request->search}%");
            });
        }

        $orders = $query->orderBy('order_date', 'desc')->paginate(20);

        return view('admin.orders.index', [
            'orders' => $orders,
            'paymentStatuses' => Order::getPaymentStatusOptions(),
            'shippingStatuses' => Order::getShippingStatusOptions(),
            'currentFilters' => [
                'status' => $request->status,
                'payment_status' => $request->payment_status,
                'search' => $request->search,
            ]
        ]);
    }

    /**
     * Display order details for admin
     */
    public function adminShow($orderId): View
    {
        $order = Order::with(['user', 'orderItems.product', 'orderItems.customProduct'])
            ->findOrFail($orderId);

        return view('admin.orders.show', [
            'order' => $order,
            'orderDetails' => $order->getFullDetails(),
            'paymentStatuses' => Order::getPaymentStatusOptions(),
            'shippingStatuses' => Order::getShippingStatusOptions(),
            'deliveryMethods' => Order::getDeliveryMethodOptions(),
        ]);
    }

    /**
     * Update order status (Admin)
     */
    public function adminUpdate(Request $request, $orderId): RedirectResponse
    {
        $request->validate([
            'payment_status' => ['sometimes', 'in:' . implode(',', Order::PAYMENT_STATUSES)],
            'shipping_status' => ['sometimes', 'in:' . implode(',', Order::SHIPPING_STATUSES)],
            'delivery_method' => ['sometimes', 'in:' . implode(',', Order::DELIVERY_METHODS)],
        ]);

        try {
            $order = Order::findOrFail($orderId);
            
            $updateData = $request->only(['payment_status', 'shipping_status', 'delivery_method']);
            $order->updateOrder($updateData);

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Order updated successfully.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update order. Please try again.');
        }
    }

    /**
     * Order analytics dashboard (Admin)
     */
    public function analytics(): View
    {
        $analytics = [
            'total_orders' => Order::getTotalCount(),
            'orders_by_status' => Order::getOrdersByStatus(),
            'revenue_stats' => Order::getRevenueStats(),
            'recent_orders' => Order::getRecentOrders(10),
        ];

        return view('admin.orders.analytics', [
            'analytics' => $analytics,
        ]);
    }
}
