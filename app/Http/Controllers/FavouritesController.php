<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavouritesController extends Controller
{
    public function addToFavorites(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $user = auth()->user();

        if ($user->favorites()->where('product_id', $request->product_id)->exists()) {
            return response()->json(['message' => 'Product already in favorites.'], 400);
        }

        $user->favorites()->create([
            'product_id' => $request->product_id,
        ]);

        return response()->json(['message' => 'Product added to favorites.']);
    }

    // Remove a product from favorites
    public function removeFromFavorites(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $user = auth()->user();

        $deleted = $user->favorites()->where('product_id', $request->product_id)->delete();

        if ($deleted) {
            return response()->json(['message' => 'Product removed from favorites.']);
        }

        return response()->json(['message' => 'Product not found in favorites.'], 404);
    }

    // Get user's favorite products
    public function getFavorites()
    {
        $user = auth()->user();

        $favorites = $user->favorites()->with('product')->get();

        return response()->json(['favorites' => $favorites]);
    }
}
