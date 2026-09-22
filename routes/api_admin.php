<?php

use App\Http\Controllers\Api\V1\Admin\ActivityLogController;
use App\Http\Controllers\Api\V1\Admin\CategoryAttributeController;
use App\Http\Controllers\Api\V1\Admin\CategoryController;
use App\Http\Controllers\Api\V1\Admin\DashboardController;
use App\Http\Controllers\Api\V1\Admin\DeliveryController;
use App\Http\Controllers\Api\V1\Admin\DriverController;
use App\Http\Controllers\Api\V1\Admin\EmployeeController;
use App\Http\Controllers\Api\V1\Admin\InventoryController;
use App\Http\Controllers\Api\V1\Admin\OrderController;
use App\Http\Controllers\Api\V1\Admin\PaymentController;
use App\Http\Controllers\Api\V1\Admin\PermissionController;
use App\Http\Controllers\Api\V1\Admin\ProductController;
use App\Http\Controllers\Api\V1\Admin\ProductImageController;
use App\Http\Controllers\Api\V1\Admin\ProductVariantController;
use App\Http\Controllers\Api\V1\Admin\PromotionController;
use App\Http\Controllers\Api\V1\Admin\RoleController;
use App\Http\Controllers\Api\V1\Admin\StockMovementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'active', 'employee'])->group(function () {

    // Auto-filtré par permission à l'intérieur du contrôleur : accessible à
    // tout employé, chaque bloc de chiffres dépendant de sa propre permission.
    Route::get('dashboard/stats', [DashboardController::class, 'stats']);

    Route::middleware('permission:employees.manage')->group(function () {
        Route::apiResource('employees', EmployeeController::class);
    });

    Route::middleware('permission:roles.manage')->group(function () {
        Route::apiResource('roles', RoleController::class)->except(['destroy']);
        Route::delete('roles/{role}', [RoleController::class, 'destroy']);
        Route::put('roles/{role}/permissions', [RoleController::class, 'syncPermissions']);
    });

    Route::middleware('permission:permissions.manage|roles.manage')->group(function () {
        Route::get('permissions', [PermissionController::class, 'index']);
    });

    Route::middleware('permission:categories.manage')->group(function () {
        Route::apiResource('categories', CategoryController::class);

        Route::apiResource('categories.attributes', CategoryAttributeController::class)
            ->except(['show'])
            ->parameters(['attributes' => 'attribute']);
    });

    Route::middleware('permission:products.manage')->group(function () {
        Route::apiResource('products', ProductController::class);

        Route::apiResource('products.variants', ProductVariantController::class)
            ->except(['show'])
            ->parameters(['variants' => 'variant']);

        Route::post('products/{product}/images', [ProductImageController::class, 'store']);
        Route::delete('products/{product}/images/{image}', [ProductImageController::class, 'destroy']);
        Route::patch('products/{product}/images/{image}/primary', [ProductImageController::class, 'setPrimary']);
    });

    Route::middleware('permission:inventory.view')->group(function () {
        Route::get('inventory', [InventoryController::class, 'index']);
        Route::get('inventory/{variant}', [InventoryController::class, 'show']);
    });

    Route::middleware('permission:inventory.update')->group(function () {
        Route::post('inventory/{variant}/adjust', [InventoryController::class, 'adjust']);
    });

    Route::middleware('permission:stock_movements.view')->group(function () {
        Route::get('stock-movements', [StockMovementController::class, 'index']);
    });

    Route::middleware('permission:orders.view')->group(function () {
        Route::get('orders', [OrderController::class, 'index']);
        Route::get('orders/{order}', [OrderController::class, 'show']);
    });

    Route::middleware('permission:orders.confirm')->group(function () {
        Route::post('orders/{order}/confirm', [OrderController::class, 'confirm']);
    });

    Route::middleware('permission:orders.update')->group(function () {
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus']);
    });

    Route::middleware('permission:orders.cancel')->group(function () {
        Route::post('orders/{order}/cancel', [OrderController::class, 'cancel']);
    });

    Route::middleware('permission:payments.view')->group(function () {
        Route::get('payments', [PaymentController::class, 'index']);
        Route::get('payments/{payment}', [PaymentController::class, 'show']);
    });

    Route::middleware('permission:payments.manage')->group(function () {
        Route::post('payments/{payment}/refund', [PaymentController::class, 'refund']);
    });

    Route::middleware('permission:deliveries.view')->group(function () {
        Route::get('deliveries', [DeliveryController::class, 'index']);
    });

    Route::middleware('permission:deliveries.manage')->group(function () {
        Route::post('deliveries/{delivery}/assign', [DeliveryController::class, 'assign']);
        Route::patch('deliveries/{delivery}/status', [DeliveryController::class, 'updateStatus']);
    });

    Route::middleware('permission:drivers.manage')->group(function () {
        Route::get('drivers', [DriverController::class, 'index']);
        Route::post('drivers', [DriverController::class, 'store']);
        Route::patch('drivers/{driver}', [DriverController::class, 'update']);
    });

    Route::middleware('permission:promotions.view')->group(function () {
        Route::get('promotions', [PromotionController::class, 'index']);
        Route::get('promotions/{promotion}', [PromotionController::class, 'show']);
    });

    Route::middleware('permission:promotions.manage')->group(function () {
        Route::post('promotions', [PromotionController::class, 'store']);
        Route::put('promotions/{promotion}', [PromotionController::class, 'update']);
        Route::delete('promotions/{promotion}', [PromotionController::class, 'destroy']);
    });

    Route::middleware('permission:activity_logs.view')->group(function () {
        Route::get('activity-logs', [ActivityLogController::class, 'index']);
    });
});
