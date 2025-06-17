<?php

namespace App\Http\Controllers;

use App\Models\CustomProduct;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Display checkout page - now supports both cart and direct purchase
     */
    public function index(Request $request): View
    {
        $checkoutType = $request->input('type', 'cart'); // 'cart' or 'direct'
        $user = auth()->user();

        if ($checkoutType === 'direct') {
            return $this->handleDirectPurchase($request, $user);
        } else {
            return $this->handleCartCheckout($user);
        }
    }

    /**
     * Handle direct product purchase checkout
     */
    private function handleDirectPurchase(Request $request, $user): View
    {
        $productId = $request->input('product_id');
        $productType = $request->input('product_type', 'ready_product');
        $quantity = max(1, min(10, (int) $request->input('quantity', 1)));

        if ($productType === 'custom_product') {
            // Handle custom product direct purchase
            $customProduct = CustomProduct::where('custom_product_id', $productId)
                ->where('user_id', $user->id)
                ->firstOrFail();

            $directProduct = [
                'product_id' => $customProduct->custom_product_id,
                'name' => $customProduct->product_name,
                'price' => $customProduct->total_price,
                'quantity' => $quantity,
                'image' => asset('images/serum-main.jpg'),
                'type' => 'custom_product',
                'category' => 'Custom Formulation'
            ];
        } else {
            // Handle ready product direct purchase
            $product = Product::where('product_id', $productId)
                ->where('is_active', true)
                ->where('is_available_for_purchase', true)
                ->firstOrFail();

            // Calculate discounted price
            $basePrice = $product->standard_price;
            $discountPercentage = $product->discount_percentage ?? 15;
            $finalPrice = $basePrice * (1 - ($discountPercentage / 100));

            $directProduct = [
                'product_id' => $product->product_id,
                'name' => $product->product_name,
                'price' => $finalPrice,
                'original_price' => $basePrice,
                'quantity' => $quantity,
                'image' => $product->image_url ?: asset('images/products/default-product.png'),
                'type' => 'ready_product',
                'category' => $product->base_category
            ];
        }

        // Calculate totals
        $subtotal = $directProduct['price'] * $quantity;
        $tax = $subtotal * 0.1; // 10% tax
        $shipping = $subtotal > 5000 ? 0 : 500; // Free shipping over LKR 5000
        $total = $subtotal + $tax + $shipping;

        return view('checkout.index', [
            'user' => $user,
            'checkoutType' => 'direct',
            'directProduct' => $directProduct,
            'cartItems' => [$directProduct], // Pass as array for template compatibility
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total
        ]);
    }

    /**
     * Handle regular cart checkout - enhanced to support both cart types
     */
    private function handleCartCheckout($user): View
    {
        // Get both custom and ready product cart items
        $customCart = session()->get('cart', []);
        $readyCart = session()->get('ready_products_cart', []);

        $customCartItems = $this->getCartItems($customCart);
        $readyCartItems = $this->getReadyCartItems($readyCart);
        $allCartItems = array_merge($customCartItems, $readyCartItems);

        if (empty($allCartItems)) {
            return redirect()->route('custom-products.index')
                ->with('error', 'Your cart is empty. Please add products to proceed with checkout.');
        }

        $subtotal = $this->calculateSubtotal($allCartItems);
        $tax = $subtotal * 0.1; // 10% tax
        $shipping = $subtotal > 5000 ? 0 : 500; // Free shipping over LKR 5000
        $total = $subtotal + $tax + $shipping;

        return view('checkout.index', [
            'user' => $user,
            'checkoutType' => 'cart',
            'directProduct' => null,
            'cartItems' => $allCartItems,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total
        ]);
    }

    /**
     * Process checkout - enhanced to handle both cart and direct purchase
     */
    public function process(Request $request): RedirectResponse
    {
        $request->validate([
            'checkout_type' => 'sometimes|in:cart,direct',
            'shipping_address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
            'payment_method' => 'required|in:card,bank_transfer,cash_on_delivery',
            'quantity' => 'required_if:checkout_type,direct|integer|min:1|max:10',
            'product_id' => 'required_if:checkout_type,direct',
            'product_type' => 'required_if:checkout_type,direct|in:ready_product,custom_product'
        ]);

        try {
            $checkoutType = $request->input('checkout_type', 'cart');

            if ($checkoutType === 'direct') {
                return $this->processDirectPurchase($request);
            } else {
                return $this->processCartCheckout($request);
            }

        } catch (\Exception $e) {
            \Log::error('Checkout error: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Failed to process your order. Please try again.');
        }
    }

    /**
     * Process direct product purchase - NOW ACTUALLY SAVES TO DATABASE
     */
    private function processDirectPurchase(Request $request): RedirectResponse
    {
        return DB::transaction(function () use ($request) {
            $user = auth()->user();
            $productId = $request->product_id;
            $productType = $request->product_type;
            $quantity = $request->quantity;

            // Get product details and calculate totals
            if ($productType === 'custom_product') {
                $customProduct = CustomProduct::where('custom_product_id', $productId)
                    ->where('user_id', $user->id)
                    ->firstOrFail();

                $itemPrice = $customProduct->total_price;
                $productData = [
                    'custom_product_id' => $customProduct->custom_product_id,
                    'product_id' => null
                ];
            } else {
                $product = Product::where('product_id', $productId)
                    ->where('is_active', true)
                    ->where('is_available_for_purchase', true)
                    ->firstOrFail();

                $basePrice = $product->standard_price;
                $discountPercentage = $product->discount_percentage ?? 15;
                $itemPrice = $basePrice * (1 - ($discountPercentage / 100));
                $productData = [
                    'product_id' => $product->product_id,
                    'custom_product_id' => null
                ];
            }

            $subtotal = $itemPrice * $quantity;
            $tax = $subtotal * 0.1;
            $shipping = $subtotal > 5000 ? 0 : 500;
            $total = $subtotal + $tax + $shipping;

            // ✅ FIXED: Create order with ONLY existing database fields
            $order = Order::create([
                'user_id' => $user->id,
                'order_date' => now(),
                'total_amount' => $total,
                'payment_status' => 'pending',
                'shipping_status' => 'processing',
                'delivery_method' => 'standard',
            ]);

            // ✅ FIXED: Create order item with ONLY existing database fields
            OrderItem::create([
                'order_id' => $order->order_id,
                'product_id' => $productData['product_id'],
                'custom_product_id' => $productData['custom_product_id'],
                'quantity' => $quantity,
                'product_price' => $itemPrice,
            ]);

            // Generate order number for display
            $orderNumber = 'CW-DP-' . str_pad($order->order_id, 5, '0', STR_PAD_LEFT);

            return redirect()->route('checkout.success')
                ->with('success', "Order placed successfully! Your order number is: {$orderNumber}")
                ->with('order_id', $order->order_id)
                ->with('order_details', [
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'shipping' => $shipping,
                    'total' => $total,
                    'payment_method' => $request->payment_method,
                    'shipping_address' => $request->shipping_address,
                    'city' => $request->city,
                    'postal_code' => $request->postal_code,
                    'phone' => $request->phone
                ]);
        });
    }

    /**
     * Process cart checkout - NOW ACTUALLY SAVES TO DATABASE
     */
    private function processCartCheckout(Request $request): RedirectResponse
    {
        return DB::transaction(function () use ($request) {
            $user = auth()->user();
            $customCart = session()->get('cart', []);
            $readyCart = session()->get('ready_products_cart', []);

            $customCartItems = $this->getCartItems($customCart);
            $readyCartItems = $this->getReadyCartItems($readyCart);
            $allCartItems = array_merge($customCartItems, $readyCartItems);

            if (empty($allCartItems)) {
                return redirect()->route('cart.index')
                    ->with('error', 'Your cart is empty.');
            }

            $subtotal = $this->calculateSubtotal($allCartItems);
            $tax = $subtotal * 0.1;
            $shipping = $subtotal > 5000 ? 0 : 500;
            $total = $subtotal + $tax + $shipping;

            // ✅ FIXED: Create order with ONLY existing database fields
            $order = Order::create([
                'user_id' => $user->id,
                'order_date' => now(),
                'total_amount' => $total,
                'payment_status' => 'pending',
                'shipping_status' => 'processing',
                'delivery_method' => 'standard',
            ]);

            // ✅ FIXED: Create order items with ONLY existing database fields
            foreach ($allCartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->order_id,
                    'product_id' => $item['type'] === 'ready_product' ? $item['id'] : null,
                    'custom_product_id' => $item['type'] === 'custom_product' ? $item['id'] : null,
                    'quantity' => $item['quantity'],
                    'product_price' => $item['price'],
                ]);
            }

            // Clear both carts after successful order
            session()->forget(['cart', 'ready_products_cart']);

            // Generate order number for display
            $orderNumber = 'CW-CART-' . str_pad($order->order_id, 5, '0', STR_PAD_LEFT);

            return redirect()->route('checkout.success')
                ->with('success', "Order placed successfully! Your order number is: {$orderNumber}")
                ->with('order_id', $order->order_id)
                ->with('order_details', [
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'shipping' => $shipping,
                    'total' => $total,
                    'payment_method' => $request->payment_method,
                    'shipping_address' => $request->shipping_address,
                    'city' => $request->city,
                    'postal_code' => $request->postal_code,
                    'phone' => $request->phone
                ]);
        });
    }
    /**
     * Show order success page
     */
    public function success(): View
    {
        return view('checkout.success');
    }

    /**
     * Get cart items with details (for custom products)
     */
    private function getCartItems(array $cart): array
    {
        $cartItems = [];

        foreach ($cart as $id => $item) {
            $cartItems[] = [
                'id' => $id,
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['price'] * $item['quantity'],
                'image' => $item['image'],
                'type' => 'custom_product',
                'category' => 'Custom Formulation'
            ];
        }

        return $cartItems;
    }

    /**
     * Get ready product cart items
     */
    private function getReadyCartItems(array $cart): array
    {
        $cartItems = [];

        foreach ($cart as $id => $item) {
            $cartItems[] = [
                'id' => $id,
                'name' => $item['name'],
                'price' => $item['price'],
                'original_price' => $item['original_price'] ?? $item['price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['price'] * $item['quantity'],
                'image' => $item['image'],
                'type' => 'ready_product',
                'category' => $item['category'] ?? 'Skincare'
            ];
        }

        return $cartItems;
    }

    /**
     * Calculate cart subtotal
     */
    private function calculateSubtotal(array $cartItems): float
    {
        return array_sum(array_column($cartItems, 'subtotal'));
    }
}