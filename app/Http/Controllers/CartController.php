<?php

// ========================================
// UPDATED CART CONTROLLER - KEY FIXES FOR CART COUNT
// ========================================

namespace App\Http\Controllers;

use App\Models\CustomProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Display the unified cart (custom + ready products)
     */
    public function index(): View
    {
        $customCartItems = $this->getCustomCartItems();
        $readyCartItems = $this->getReadyCartItems();

        // Combine both cart types
        $allCartItems = array_merge($customCartItems, $readyCartItems);

        $subtotal = $this->calculateSubtotal($allCartItems);
        $tax = $subtotal * 0.1; // 10% tax
        $shipping = $subtotal > 5000 ? 0 : 500; // Free shipping over LKR 5000
        $total = $subtotal + $tax + $shipping;

        return view('cart.index', [
            'cartItems' => $allCartItems,
            'customCartItems' => $customCartItems,
            'readyCartItems' => $readyCartItems,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total,
            'itemCount' => count($allCartItems)
        ]);
    }

    /**
     * Add ready product to cart (FIXED VERSION)
     */
    public function addReadyProduct(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,product_id',
                'quantity' => 'required|integer|min:1|max:10'
            ]);

            $product = Product::where('product_id', $request->product_id)
                ->where('is_active', true)
                ->firstOrFail();

            // Get current ready products cart from session
            $cart = session()->get('ready_products_cart', []);
            $productId = $request->product_id;

            // Calculate discounted price
            $basePrice = $product->standard_price;
            $discountPercentage = $product->discount_percentage ?? 15;
            $finalPrice = $basePrice * (1 - ($discountPercentage / 100));

            if (isset($cart[$productId])) {
                $cart[$productId]['quantity'] += $request->quantity;
            } else {
                $cart[$productId] = [
                    'product_id' => $product->product_id,
                    'name' => $product->product_name,
                    'category' => $product->base_category,
                    'original_price' => $basePrice,
                    'price' => $finalPrice,
                    'quantity' => $request->quantity,
                    'image' => $product->image_url ?: asset('images/products/default-product.png'),
                    'type' => 'ready_product'
                ];
            }

            session()->put('ready_products_cart', $cart);

            // Calculate total cart count (FIXED - this was the main issue)
            $totalCartCount = $this->getTotalCartCount();

            \Log::info('Ready product added to cart:', [
                'product_id' => $productId,
                'product_name' => $product->product_name,
                'quantity' => $request->quantity,
                'total_cart_count' => $totalCartCount,
                'custom_cart_items' => array_sum(array_column(session()->get('cart', []), 'quantity')),
                'ready_cart_items' => array_sum(array_column(session()->get('ready_products_cart', []), 'quantity'))
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully!',
                'cart_count' => $totalCartCount,
                'product_added' => [
                    'id' => $product->product_id,
                    'name' => $product->product_name,
                    'quantity' => $request->quantity,
                    'price' => $finalPrice
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error adding ready product to cart: ' . $e->getMessage(), [
                'product_id' => $request->product_id ?? 'unknown',
                'quantity' => $request->quantity ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to add product to cart'
            ], 500);
        }
    }

    /**
     * Add custom product to cart (UPDATED)
     */
    public function addCustomProduct(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'custom_product_id' => 'required|exists:custom_products,custom_product_id',
                'quantity' => 'required|integer|min:1|max:10'
            ]);

            $user = auth()->user();
            $customProduct = CustomProduct::where('custom_product_id', $request->custom_product_id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            // Get current custom cart from session
            $cart = session()->get('cart', []);
            $productId = $request->custom_product_id;

            if (isset($cart[$productId])) {
                $cart[$productId]['quantity'] += $request->quantity;
            } else {
                $cart[$productId] = [
                    'custom_product_id' => $customProduct->custom_product_id,
                    'name' => $customProduct->product_name,
                    'price' => $customProduct->total_price,
                    'quantity' => $request->quantity,
                    'image' => asset('images/serum-main.jpg'),
                    'type' => 'custom_product'
                ];
            }

            session()->put('cart', $cart);

            // Calculate total cart count
            $totalCartCount = $this->getTotalCartCount();

            \Log::info('Custom product added to cart:', [
                'custom_product_id' => $productId,
                'product_name' => $customProduct->product_name,
                'quantity' => $request->quantity,
                'total_cart_count' => $totalCartCount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Custom product added to cart successfully!',
                'cart_count' => $totalCartCount,
                'product_type' => 'custom'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error adding custom product to cart: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add product to cart'
            ], 500);
        }
    }

    /**
     * Update cart item quantity (FIXED)
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'quantity' => 'required|integer|min:1|max:10',
                'type' => 'required|in:custom_product,ready_product'
            ]);

            $updated = false;

            if ($request->type === 'custom_product') {
                $cart = session()->get('cart', []);
                if (isset($cart[$id])) {
                    $cart[$id]['quantity'] = $request->quantity;
                    session()->put('cart', $cart);
                    $updated = true;
                }
            } else {
                $cart = session()->get('ready_products_cart', []);
                if (isset($cart[$id])) {
                    $cart[$id]['quantity'] = $request->quantity;
                    session()->put('ready_products_cart', $cart);
                    $updated = true;
                }
            }

            if ($updated) {
                $totalCartCount = $this->getTotalCartCount();

                \Log::info('Cart item updated:', [
                    'item_id' => $id,
                    'type' => $request->type,
                    'new_quantity' => $request->quantity,
                    'total_cart_count' => $totalCartCount
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Cart updated successfully',
                    'cart_count' => $totalCartCount
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Product not found in cart'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Error updating cart: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cart'
            ], 500);
        }
    }

    /**
     * Remove item from cart (FIXED)
     */
    public function remove(Request $request, $id): JsonResponse
    {
        try {
            $type = $request->input('type', 'custom_product');
            $removed = false;

            if ($type === 'custom_product') {
                $cart = session()->get('cart', []);
                if (isset($cart[$id])) {
                    unset($cart[$id]);
                    session()->put('cart', $cart);
                    $removed = true;
                }
            } else {
                $cart = session()->get('ready_products_cart', []);
                if (isset($cart[$id])) {
                    unset($cart[$id]);
                    session()->put('ready_products_cart', $cart);
                    $removed = true;
                }
            }

            if ($removed) {
                $totalCartCount = $this->getTotalCartCount();

                \Log::info('Cart item removed:', [
                    'item_id' => $id,
                    'type' => $type,
                    'total_cart_count' => $totalCartCount
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Product removed from cart',
                    'cart_count' => $totalCartCount
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Product not found in cart'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Error removing from cart: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove product from cart'
            ], 500);
        }
    }

    /**
     * Clear entire cart (FIXED)
     */
    public function clear(): JsonResponse
    {
        try {
            session()->forget(['cart', 'ready_products_cart']);

            \Log::info('Cart cleared completely');

            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully',
                'cart_count' => 0
            ]);
        } catch (\Exception $e) {
            \Log::error('Error clearing cart: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cart'
            ], 500);
        }
    }

    /**
     * Get cart summary (ENHANCED)
     */
    public function summary(): JsonResponse
    {
        try {
            $customCartItems = $this->getCustomCartItems();
            $readyCartItems = $this->getReadyCartItems();
            $allCartItems = array_merge($customCartItems, $readyCartItems);

            $subtotal = $this->calculateSubtotal($allCartItems);
            $tax = $subtotal * 0.1;
            $shipping = $subtotal > 5000 ? 0 : 500;
            $total = $subtotal + $tax + $shipping;

            $totalCartCount = $this->getTotalCartCount();

            return response()->json([
                'success' => true,
                'cart_count' => $totalCartCount,
                'cart_items' => count($allCartItems),
                'custom_items' => count($customCartItems),
                'ready_items' => count($readyCartItems),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping' => $shipping,
                'total' => $total,
                'items' => $allCartItems
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting cart summary: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get cart summary'
            ], 500);
        }
    }

    /**
     * Get total cart count (both custom and ready products) - FIXED
     */
    private function getTotalCartCount(): int
    {
        $customCart = session()->get('cart', []);
        $readyCart = session()->get('ready_products_cart', []);

        $customCount = array_sum(array_column($customCart, 'quantity'));
        $readyCount = array_sum(array_column($readyCart, 'quantity'));

        $total = $customCount + $readyCount;

        \Log::debug('Cart count calculation:', [
            'custom_count' => $customCount,
            'ready_count' => $readyCount,
            'total_count' => $total,
            'custom_cart_keys' => array_keys($customCart),
            'ready_cart_keys' => array_keys($readyCart)
        ]);

        return $total;
    }

    /**
     * Get custom product cart items
     */
    private function getCustomCartItems(): array
    {
        $cart = session()->get('cart', []);
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
    private function getReadyCartItems(): array
    {
        $cart = session()->get('ready_products_cart', []);
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