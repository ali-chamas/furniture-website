<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FavouritesController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//AUTH
Route::post('auth/signup', [AuthController::class, 'signup']);
Route::post('auth/login', [AuthController::class, 'login']);

//USER
Route::middleware([JwtMiddleware::class])->group(function () {
    Route::put('user/{id}', [UserController::class, 'update']);
    Route::delete('user/{id}', [UserController::class, 'delete']);
    Route::post('user/{id}/ban', [UserController::class, 'ban']);
    Route::get('/user', [UserController::class, 'getUser']);
});

Route::middleware([JwtMiddleware::class,AdminMiddleware::class])->group(function () {
    Route::get('/admin/users', [UserController::class, 'getUsers']);
});

//PRODUCTS

Route::get('/products', [ProductController::class, 'getProducts']); 
Route::get('/products/{id}', [ProductController::class, 'getProduct']); 


Route::middleware([JwtMiddleware::class, AdminMiddleware::class])->group(function () {
    Route::post('/products', [ProductController::class, 'create']); 
    Route::post('/products/{id}', [ProductController::class, 'update']); 
    Route::delete('/products/{id}', [ProductController::class, 'delete']); 
});

//ORDERS

Route::middleware([JwtMiddleware::class])->group(function () {
    Route::get('orders', [OrderController::class, 'getUserOrders']);
    Route::post('orders/{id}/cancel', [OrderController::class, 'cancelOrder']);
});

Route::middleware([JwtMiddleware::class, AdminMiddleware::class])->group(function () {
    Route::get('admin/orders', [OrderController::class, 'getAllOrders']);
    Route::post('admin/orders/{id}/status', [OrderController::class, 'updateOrderStatus']);
});

//FAVOURITES
Route::middleware([JwtMiddleware::class])->group(function () {
    Route::post('favorites/add', [FavouritesController::class, 'addToFavorites']);
    Route::delete('favorites/remove', [FavouritesController::class, 'removeFromFavorites']);
    Route::get('favorites', [FavouritesController::class, 'getFavorites']);


   
});

//CART
Route::middleware([JwtMiddleware::class])->group(function () {
    Route::post('cart/add', [CartController::class, 'addToCart']);
    Route::post('cart/remove', [CartController::class, 'removeFromCart']);
    Route::get('cart', [CartController::class, 'viewCart']);
    Route::post('cart/checkout', [CartController::class, 'checkout']);
});

//PRODUCTIMAGE
Route::middleware([JwtMiddleware::class, AdminMiddleware::class])->group(function () {
    Route::post('products/{productId}/images', [ProductImageController::class, 'addImages']);
    Route::delete('products/{productId}/images/{imageId}', [ProductImageController::class, 'deleteImage']);
});