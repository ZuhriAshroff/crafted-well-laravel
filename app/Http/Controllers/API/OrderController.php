<?php

// 1. API CONTROLLER - app/Http/Controllers/API/OrderController.php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Apply middleware for authentication
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('active.user');
    }

    /**
     * Display a listing of user's orders.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $page = $request->get('page', 1);
            $perPage = min($request->get('per_page', 10), 50);

            $orders = Order::forUser($user->user_id)
                ->with(['orderItems.product', 'orderItems.customProduct'])
                ->orderBy('order_date', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            $formattedOrders = $orders->getCollection()->map(function ($order) {
                return $order->getFullDetails();
            });

            return response()->json([
                'status' => 'success',
                'data' => $formattedOrders,
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                    'last_page' => $orders->lastPage(),
                    'has_more' => $orders->hasMorePages(),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching user orders: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch orders'
            ], 500);
        }
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Validate the request data
            $validator = Validator::make($request->all(), Order::validationRules());

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();
            $data['user_id'] = $user->user_id;

            // Create order with items in transaction
            $order = Order::createWithItems($data);

            // Log order creation for analytics
            $this->logOrderAnalytics($user, 'order_created', [
                'order_id' => $order->order_id,
                'total_amount' => $order->total_amount,
                'item_count' => count($data['items']),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Order created successfully',
                'data' => $order->getFullDetails()
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Error creating order: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create order'
            ], 500);
        }
    }

    /**
     * Display the specified order.
     */
    public function show(Request $request, $orderId): JsonResponse
    {
        try {
            $user = $request->user();

            $order = Order::forUser($user->user_id)->findOrFail($orderId);

            // Log order view for analytics
            $this->logOrderAnalytics($user, 'order_viewed', [
                'order_id' => $order->order_id,
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $order->getFullDetails()
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Error fetching order: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch order'
            ], 500);
        }
    }

    /**
     * Update the specified order (limited fields).
     */
    public function update(Request $request, $orderId): JsonResponse
    {
        try {
            $user = $request->user();

            $order = Order::forUser($user->user_id)->findOrFail($orderId);

            // Only allow certain status updates by regular users
            $allowedFields = ['delivery_method'];
            
            // Validate only allowed fields
            $validator = Validator::make($request->only($allowedFields), [
                'delivery_method' => ['sometimes', 'string', 'in:standard,express,pickup']
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if order can be modified
            if (!$order->canBeCancelled()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order cannot be modified in its current status'
                ], 400);
            }

            $order->updateOrder($validator->validated());

            // Log order update
            $this->logOrderAnalytics($user, 'order_updated', [
                'order_id' => $order->order_id,
                'updated_fields' => array_keys($validator->validated()),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Order updated successfully',
                'data' => $order->fresh()->getFullDetails()
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found'
            ], 404);

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Error updating order: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update order'
            ], 500);
        }
    }

    /**
     * Cancel an order (soft delete - change status).
     */
    public function cancel(Request $request, $orderId): JsonResponse
    {
        try {
            $user = $request->user();

            $order = Order::forUser($user->user_id)->findOrFail($orderId);

            if (!$order->canBeCancelled()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order cannot be cancelled in its current status'
                ], 400);
            }

            $order->updateOrder([
                'shipping_status' => 'cancelled'
            ]);

            // Log order cancellation
            $this->logOrderAnalytics($user, 'order_cancelled', [
                'order_id' => $order->order_id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Order cancelled successfully',
                'data' => $order->fresh()->getFullDetails()
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Error cancelling order: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to cancel order'
            ], 500);
        }
    }

    /**
     * Get order tracking information.
     */
    public function tracking(Request $request, $orderId): JsonResponse
    {
        try {
            $user = $request->user();

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

            return response()->json([
                'status' => 'success',
                'data' => [
                    'order' => $order->getFullDetails(),
                    'tracking_steps' => array_values($trackingSteps),
                    'current_status' => $order->getDisplayStatus(),
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Error fetching order tracking: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch tracking information'
            ], 500);
        }
    }

    /**
     * Get order statistics for user.
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $stats = [
                'total_orders' => Order::forUser($user->user_id)->count(),
                'completed_orders' => Order::forUser($user->user_id)->byShippingStatus('delivered')->count(),
                'pending_orders' => Order::forUser($user->user_id)->byShippingStatus('processing')->count(),
                'total_spent' => Order::forUser($user->user_id)->byPaymentStatus('paid')->sum('total_amount'),
                'average_order_value' => Order::forUser($user->user_id)->byPaymentStatus('paid')->avg('total_amount'),
                'recent_orders' => Order::forUser($user->user_id)->recent(3)->get()->map(fn($order) => $order->getFullDetails()),
            ];

            return response()->json([
                'status' => 'success',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching order statistics: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch statistics'
            ], 500);
        }
    }

    /**
     * Admin-only endpoints
     */

    /**
     * Get all orders (Admin only).
     */
    public function adminIndex(Request $request): JsonResponse
    {
        // Apply admin middleware in routes
        try {
            $page = $request->get('page', 1);
            $perPage = min($request->get('per_page', 20), 100);
            $status = $request->get('status');
            $paymentStatus = $request->get('payment_status');

            $query = Order::with(['user:user_id,first_name,last_name,email', 'orderItems']);

            if ($status) {
                $query->byShippingStatus($status);
            }

            if ($paymentStatus) {
                $query->byPaymentStatus($paymentStatus);
            }

            $orders = $query->orderBy('order_date', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            $formattedOrders = $orders->getCollection()->map(function ($order) {
                return $order->getFullDetails();
            });

            return response()->json([
                'status' => 'success',
                'data' => $formattedOrders,
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                    'last_page' => $orders->lastPage(),
                    'has_more' => $orders->hasMorePages(),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching orders for admin: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch orders'
            ], 500);
        }
    }

    /**
     * Update order status (Admin only).
     */
    public function adminUpdate(Request $request, $orderId): JsonResponse
    {
        try {
            $order = Order::findOrFail($orderId);

            // Admin can update all order fields
            $validator = Validator::make($request->all(), [
                'payment_status' => ['sometimes', 'string', 'in:' . implode(',', Order::PAYMENT_STATUSES)],
                'shipping_status' => ['sometimes', 'string', 'in:' . implode(',', Order::SHIPPING_STATUSES)],
                'delivery_method' => ['sometimes', 'string', 'in:' . implode(',', Order::DELIVERY_METHODS)],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $order->updateOrder($validator->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Order updated successfully',
                'data' => $order->fresh()->getFullDetails()
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Error updating order (admin): ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update order'
            ], 500);
        }
    }

    /**
     * Get order analytics (Admin only).
     */
    public function analytics(Request $request): JsonResponse
    {
        try {
            $analytics = [
                'total_orders' => Order::getTotalCount(),
                'orders_by_status' => Order::getOrdersByStatus(),
                'revenue_stats' => Order::getRevenueStats(),
                'recent_orders' => Order::getRecentOrders(10),
            ];

            return response()->json([
                'status' => 'success',
                'data' => $analytics
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching order analytics: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch analytics'
            ], 500);
        }
    }

    /**
     * Log analytics to MongoDB
     */
    private function logOrderAnalytics($user, $action, $data = [])
    {
        try {
            $analyticsData = [
                'user_id' => $user->user_id,
                'action' => $action,
                'timestamp' => now(),
                'user_agent' => request()->userAgent(),
                'ip_address' => request()->ip(),
                'additional_data' => $data
            ];

            // MongoDB Service integration
            // MongoService::logOrderActivity($analyticsData);
            
        } catch (\Exception $e) {
            \Log::error('Failed to log order analytics: ' . $e->getMessage());
        }
    }
}
