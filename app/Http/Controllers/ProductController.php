<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Storage;
use Validator;

class ProductController extends Controller
{
    public function create(Request $request)
{
    // Validate incoming request
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric',
        'category_id' => 'required|exists:categories,id',
        'images' => 'nullable|array', // Allow images to be optional
        'images.*' => 'file|mimes:jpeg,png,jpg|max:2048', // Validate each image file type
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Store product details in the database first
    $product = Product::create([
        'name' => $request->name,
        'description' => $request->description,
        'price' => $request->price,
        'category_id' => $request->category_id,
        'stock_quantity' => $request->stock_quantity, // assuming you are passing stock_quantity
    ]);

    // Handle file uploads for images
    if ($request->hasFile('images')) {
        $imagePaths = [];
        foreach ($request->file('images') as $image) {
            // Store each image in the public disk and get its URI
            $path = $image->store('product_images', 'public');
            $imagePaths[] = Storage::url($path); // Store the URI for the image
        }

        // Assuming you have a relationship set up for product images (one-to-many)
        foreach ($imagePaths as $imageUri) {
            ProductImage::create([
                'product_id' => $product->id,
                'image_url' => $imageUri,
                'is_primary' => false,
            ]);
        }
    }

    // Return success response
    return response()->json([
        'message' => 'Product created successfully!',
        'product' => $product,
    ], 201);
}



  
public function getProducts()
{
    try {
        $products = Product::get();

        return response()->json([
            'message' => 'Products retrieved successfully.',
            'products' => $products,
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'An error occurred while fetching products.',
            'error' => $e->getMessage(),
        ], 500);
    }
 
}


public function getProduct($id)
{
    $product = Product::with(['category', 'images'])->find($id);

    if (!$product) {
        return response()->json(['message' => 'Product not found'], 404);
    }

    return response()->json($product, 200);
}


   
public function update(Request $request, $id)
{
    // Validation rules
    $rules = [
        'name' => 'nullable|unique:products,name,' . $id . '|max:255',
        'description' => 'nullable|max:1000',
        'price' => 'nullable|numeric|min:0',
        'category_id' => 'nullable|exists:categories,id',
        'stock_quantity' => 'nullable|integer|min:0',
        'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ];

    // Validate the request data
    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    try {
        // Find the product by ID or fail
        $product = Product::findOrFail($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        // Update the product fields, excluding images
        $product->update([
            'name' => $request->name ?? $product->name,
            'description' => $request->description ?? $product->description,
            'price' => $request->price ?? $product->price,
            'category_id' => $request->category_id ?? $product->category_id,
            'stock_quantity' => $request->stock_quantity ?? $product->stock_quantity,
        ]);



        // Handle image uploads if images are provided in the request
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('product_images', 'public');
                $product->images()->create([
                    'image_url' => Storage::url($path), // Use Storage::url() to get the full URL to the image
                    'is_primary' => $index === 0, // Mark the first image as primary
                ]);
            }
        }

        // Return a success response with the updated product details
        return response()->json([
            'message' => 'Product updated successfully.',
            'product' => $request->name, // Load images relationship if needed
        ]);
    } catch (\Exception $e) {
        // Handle any exceptions and return an error response
        return response()->json([
            'message' => 'An error occurred while updating the product.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


public function delete($id)
{
    try {
        $product = Product::findOrFail($id);

        $product->images()->delete();
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully.']);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'An error occurred while deleting the product.',
            'error' => $e->getMessage(),
        ], 500);
    }
}



}
