<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CheckoutController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Display checkout page
     */
    public function index(): View
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('custom-products.index')
                ->with('error', 'Your cart is empty. Please add products to proceed with checkout.');
        }

        $cartItems = $this->getCartItems($cart);
        $subtotal = $this->calculateSubtotal($cartItems);
        $tax = $subtotal * 0.1; // 10% tax
        $shipping = 5.00; // Fixed shipping cost
        $total = $subtotal + $tax + $shipping;

        return view('checkout.index', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total,
            'user' => auth()->user()
        ]);
    }

    /**
     * Process checkout
     */
    public function process(Request $request): RedirectResponse
    {
        $request->validate([
            'shipping_address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
            'payment_method' => 'required|in:card,bank_transfer,cash_on_delivery'
        ]);

        try {
            $cart = session()->get('cart', []);
            
            if (empty($cart)) {
                return redirect()->route('cart.index')
                    ->with('error', 'Your cart is empty.');
            }

            // Here you would:
            // 1. Create an order record
            // 2. Process payment (if not COD)
            // 3. Send confirmation email
            // 4. Clear cart

            // For now, just simulate successful order
            $orderNumber = 'CW' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
            
            // Clear cart after successful order
            session()->forget('cart');

            return redirect()->route('checkout.success')
                ->with('success', "Order placed successfully! Your order number is: {$orderNumber}");

        } catch (\Exception $e) {
            \Log::error('Checkout error: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Failed to process your order. Please try again.');
        }
    }

    /**
     * Show order success page
     */
    public function success(): View
    {
        return view('checkout.success');
    }

    /**
     * Get cart items with details
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