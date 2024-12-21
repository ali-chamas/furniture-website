<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    
    // public function createOrder()
    // {
    //     $user = auth()->user();

    //     // Get the user's cart items
    //     $cartItems = $user->cart()->with('product')->get();

    //     if ($cartItems->isEmpty()) {
    //         return response()->json(['message' => 'Cart is empty.'], 400);
    //     }

    //     $total = 0;

    //     // Calculate the total and validate stock
    //     foreach ($cartItems as $cartItem) {
    //         $product = $cartItem->product;

    //         if ($product->stock_quantity < $cartItem->quantity) {
    //             return response()->json([
    //                 'message' => "Insufficient stock for product: {$product->name}.",
    //             ], 400);
    //         }

    //         $total += $cartItem->quantity * $product->price;
    //     }

    //     // Create the order
    //     $order = Order::create([
    //         'user_id' => $user->id,
    //         'order_date' => now(),
    //         'status_id' => 1, // Pending status
    //         'total' => $total,
    //     ]);

    //     // Create order items and reduce product stock
    //     foreach ($cartItems as $cartItem) {
    //         $product = $cartItem->product;

    //         OrderItem::create([
    //             'order_id' => $order->id,
    //             'product_id' => $product->id,
    //             'quantity' => $cartItem->quantity,
    //             'price' => $product->price,
    //         ]);

    //         // Reduce stock
    //         $product->stock_quantity -= $cartItem->quantity;
    //         $product->save();
    //     }

    //     // Clear user's cart
    //     $user->cart()->delete();

    //     return response()->json([
    //         'message' => 'Order created successfully.',
    //         'order' => $order->load('orderItems.product', 'status'),
    //     ]);
    // }

    // Get user's orders
    public function getUserOrders()
    {
        $user = auth()->user();

        $orders = $user->orders()->with('orderItems.product', 'status')->get();

        return response()->json(['orders' => $orders]);
    }

    // Cancel an order (User only)
    public function cancelOrder($id)
    {
        $user = auth()->user();

        $order = $user->orders()->where('id', $id)->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        if ($order->status_id != 1) { 
            return response()->json(['message' => 'Order cannot be canceled.'], 400);
        }

        $order->update(['status_id' => 3]);

        return response()->json(['message' => 'Order canceled successfully.']);
    }

    // Admin: View all orders
    public function getAllOrders()
    {
        $orders = Order::with('orderItems.product', 'user', 'status')->get();

        return response()->json(['orders' => $orders]);
    }

    // Admin: Update order status
    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'status_id' => 'required|exists:order_status,id',
        ]);

        $order = Order::findOrFail($id);

        $order->update(['status_id' => $request->status_id]);

        return response()->json(['message' => 'Order status updated successfully.', 'order' => $order]);
    }


}
