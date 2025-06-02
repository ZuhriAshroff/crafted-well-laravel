<?php

namespace App\Http\Controllers;

use App\Models\CustomProduct;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Display the cart
     */
    public function index(): View
    {
        $cartItems = $this->getCartItems();
        $subtotal = $this->calculateSubtotal($cartItems);
        $tax = $subtotal * 0.1; // 10% tax
        $total = $subtotal + $tax;

        return view('cart.index', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total
        ]);
    }

    /**
     * Add item to cart
     */
    public function add(Request $request): JsonResponse
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

            // Get current cart from session
            $cart = session()->get('cart', []);
            $productId = $request->custom_product_id;

            if (isset($cart[$productId])) {
                // Update quantity if product already in cart
                $cart[$productId]['quantity'] += $request->quantity;
            } else {
                // Add new product to cart
                $cart[$productId] = [
                    'custom_product_id' => $customProduct->custom_product_id,
                    'name' => $customProduct->product_name,
                    'price' => $customProduct->total_price,
                    'quantity' => $request->quantity,
                    'image' => asset('images/serum-main.jpg')
                ];
            }

            session()->put('cart', $cart);

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully!',
                'cart_count' => array_sum(array_column($cart, 'quantity'))
            ]);

        } catch (\Exception $e) {
            \Log::error('Error adding to cart: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add product to cart'
            ], 500);
        }
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'quantity' => 'required|integer|min:1|max:10'
            ]);

            $cart = session()->get('cart', []);
            
            if (isset($cart[$id])) {
                $cart[$id]['quantity'] = $request->quantity;
                session()->put('cart', $cart);

                return response()->json([
                    'success' => true,
                    'message' => 'Cart updated successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Product not found in cart'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cart'
            ], 500);
        }
    }

    /**
     * Remove item from cart
     */
    public function remove($id): JsonResponse
    {
        try {
            $cart = session()->get('cart', []);
            
            if (isset($cart[$id])) {
                unset($cart[$id]);
                session()->put('cart', $cart);

                return response()->json([
                    'success' => true,
                    'message' => 'Product removed from cart'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Product not found in cart'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove product from cart'
            ], 500);
        }
    }

    /**
     * Clear entire cart
     */
    public function clear(): JsonResponse
    {
        session()->forget('cart');
        
        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully'
        ]);
    }

    /**
     * Get cart items with product details
     */
    private function getCartItems(): array
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
                'image' => $item['image']
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