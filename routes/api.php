<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\WishlistController;

/*
|--------------------------------------------------------------------------
| API Routes - متجر الأجهزة الإلكترونية
|--------------------------------------------------------------------------
*/

// ===== Public Routes (لا تحتاج تسجيل دخول) =====

// المصادقة
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);
});

// الفئات
Route::get('categories',         [CategoryController::class, 'index']);
Route::get('categories/{slug}',  [CategoryController::class, 'show']);

// المنتجات
Route::prefix('products')->group(function () {
    Route::get('/',             [ProductController::class, 'index']);       // كل المنتجات
    Route::get('featured',      [ProductController::class, 'featured']);    // مقترح لك
    Route::get('best-sellers',  [ProductController::class, 'bestSellers']); // الأفضل مبيعاً
    Route::get('flash-sale',    [ProductController::class, 'flashSale']);   // Flash Sale
    Route::get('{slug}',        [ProductController::class, 'show']);        // تفاصيل منتج
});

// التقييمات العامة
Route::get('products/{id}/reviews', [ReviewController::class, 'index']);


// ===== Protected Routes (تحتاج تسجيل دخول) =====

Route::middleware('auth:sanctum')->group(function () {

    // المستخدم
    Route::prefix('auth')->group(function () {
        Route::get('me',      [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });

    // السلة
    Route::apiResource('cart', CartController::class)->except(['show']);

    // الطلبات
    Route::apiResource('orders', OrderController::class)->only(['index', 'store', 'show']);

    // التقييمات
    Route::post('products/{id}/reviews', [ReviewController::class, 'store']);

    // المفضلة
    Route::post('wishlist/{productId}',   [WishlistController::class, 'toggle']);
    Route::get('wishlist',                [WishlistController::class, 'index']);
    Route::delete('wishlist/clear',  [WishlistController::class, 'clear']);
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy']);
});
