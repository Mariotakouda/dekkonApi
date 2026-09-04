<?php

use App\Http\Controllers\Api\V1\Client\AddressController;
use App\Http\Controllers\Api\V1\Client\CartController;
use App\Http\Controllers\Api\V1\Client\CategoryController;
use App\Http\Controllers\Api\V1\Client\FavoriteController;
use App\Http\Controllers\Api\V1\Client\NotificationController;
use App\Http\Controllers\Api\V1\Client\OrderController;
use App\Http\Controllers\Api\V1\Client\PaymentController;
use App\Http\Controllers\Api\V1\Client\ProductController;
use App\Http\Controllers\Api\V1\Client\ProfileController;
use App\Http\Controllers\Api\V1\Client\ReviewController;
use Illuminate\Support\Facades\Route;

// Catalogue — public, pas d'authentification requise
Route::get('categories', [CategoryController::class, 'index']);
Route::get('products', [ProductController::class, 'index']);
Route::get('products/{product}', [ProductController::class, 'show']);

// Webhook Fedapay — public, appelé par Fedapay lui-même
Route::post('payments/callback', [PaymentController::class, 'callback']);

// Espace Client authentifié
Route::middleware(['auth:sanctum', 'active'])->group(function () {

    Route::get('profile', [ProfileController::class, 'show']);
    Route::patch('profile', [ProfileController::class, 'update']);

    Route::apiResource('addresses', AddressController::class);

    Route::get('favorites', [FavoriteController::class, 'index']);
    Route::post('favorites/{product}', [FavoriteController::class, 'store']);
    Route::delete('favorites/{product}', [FavoriteController::class, 'destroy']);
    Route::get('favorites/{product}/check', [FavoriteController::class, 'check']);

    Route::get('cart', [CartController::class, 'show']);
    Route::post('cart/items', [CartController::class, 'addItem']);
    Route::patch('cart/items/{itemId}', [CartController::class, 'updateItem']);
    Route::delete('cart/items/{itemId}', [CartController::class, 'removeItem']);

    Route::get('orders', [OrderController::class, 'index']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders/{order}', [OrderController::class, 'show']);
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancel']);
    Route::post('orders/{order}/payment/initiate', [PaymentController::class, 'initiate']);

    Route::get('reviews', [ReviewController::class, 'index']);
    Route::post('reviews', [ReviewController::class, 'store']);

    Route::get('notifications', [NotificationController::class, 'index']);
    Route::patch('notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::patch('notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount']);

});
