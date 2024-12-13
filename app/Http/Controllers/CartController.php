<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = auth()->user();

        $cartItem = $user->cart()->updateOrCreate(
            ['product_id' => $request->product_id],
            ['quantity' => $request->quantity]
        );

        return response()->json(['message' => 'Product added to cart.', 'cart' => $cartItem]);
    }

    // Remove a product from cart
    public function removeFromCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $user = auth()->user();

        $deleted = $user->cart()->where('product_id', $request->product_id)->delete();

        if ($deleted) {
            return response()->json(['message' => 'Product removed from cart.']);
        }

        return response()->json(['message' => 'Product not found in cart.'], 404);
    }

    // View cart
    public function viewCart()
    {
        $user = auth()->user();

        $cartItems = $user->cart()->with('product')->get();

        return response()->json(['cart' => $cartItems]);
    }

    // Checkout (clear the cart and reduce stock)
    public function checkout()
{
    $user = auth()->user();

    // Get the user's cart items
    $cartItems = $user->cart()->with('product')->get();

    if ($cartItems->isEmpty()) {
        return response()->json(['message' => 'Cart is empty.'], 400);
    }

    $total = 0;

    // Calculate the total and validate stock
    foreach ($cartItems as $cartItem) {
        $product = $cartItem->product;

        if ($product->stock_quantity < $cartItem->quantity) {
            return response()->json([
                'message' => "Insufficient stock for product: {$product->name}.",
            ], 400);
        }

        $total += $cartItem->quantity * $product->price;
    }

    try {
        // Begin transaction
        \DB::beginTransaction();

        // Create the order
        $order = Order::create([
            'user_id' => $user->id,
            'order_date' => now(),
            'status_id' => 1, // Pending status
            'total' => $total,
        ]);

        // Create order items and reduce product stock
        foreach ($cartItems as $cartItem) {
            $product = $cartItem->product;

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $cartItem->quantity,
                'price' => $product->price,
            ]);

            // Reduce stock
            $product->stock_quantity -= $cartItem->quantity;
            $product->save();
        }

        // Clear user's cart
        $user->cart()->delete();

        // Commit transaction
        \DB::commit();

        return response()->json([
            'message' => 'Checkout completed and order created successfully.',
            'order' => $order->load('orderItems.product', 'status'),
        ]);
    } catch (\Exception $e) {
        // Rollback transaction on failure
        \DB::rollBack();

        return response()->json([
            'message' => 'An error occurred during checkout.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

}
