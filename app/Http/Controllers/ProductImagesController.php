<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Storage;
use Validator;

class ProductImagesController extends Controller
{
    public function addImages(Request $request, $productId)
{
    $validator = Validator::make($request->all(), [
        'images' => 'required|array',
        'images.*' => 'file|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    try {
        $product = Product::findOrFail($productId);

        $imagePaths = [];
        foreach ($request->file('images') as $image) {
            
            $path = $image->store('product_images', 'public');
            $imagePaths[] = Storage::url($path);

           
            $product->images()->create([
                'image_url' => $path,
                'is_primary' => false
            ]);
        }

        return response()->json([
            'message' => 'Images added successfully.',
            'images' => $product->images()->get(),
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'An error occurred while adding images.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function deleteImage($productId, $imageId)
{
    try {
        $product = Product::findOrFail($productId);

        $image = $product->images()->findOrFail($imageId);

    

        $image->delete();

        return response()->json([
            'message' => 'Image deleted successfully.',
            'images' => $product->images()->get()
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'An error occurred while deleting the image.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


}
