<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/products', [ProductController::class, 'index']);

Route::get('/products/{id}', [ProductController::class, 'show']);

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');

Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword'])
    ->middleware('throttle:5,1');

Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
    ->middleware('throttle:5,1');

Route::post('/products', [ProductController::class, 'store'])
    ->middleware(['auth:sanctum', 'can:manage-products']);

Route::patch('/products/{product}', [ProductController::class, 'update'])
    ->middleware(['auth:sanctum', 'can:manage-products']);
// category

Route::get('/categories', [CategoryController::class, 'index']);

Route::post('/categories', [CategoryController::class, 'store'])
    ->middleware(['auth:sanctum', 'can:manage-categories']);

// put
Route::put('/products/{product}/categories', [ProductController::class, 'syncCategories'])
    ->middleware(['auth:sanctum', 'can:manage-products']);
// cart
Route::get('/cart', [CartController::class, 'show'])
    ->middleware('auth:sanctum');

Route::post('/cart/items', [CartController::class, 'addItem'])
    ->middleware('auth:sanctum');

Route::patch('/cart/items/{item}', [CartController::class, 'updateItem'])
    ->middleware('auth:sanctum');

Route::delete('/cart/items/{item}', [CartController::class, 'removeItem'])
    ->middleware('auth:sanctum');
