<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CacaoBatchesController;
use App\Http\Controllers\CacaoPurchasesController;
use App\Http\Controllers\CapitalRecordsController;
use App\Http\Controllers\CartItemsController;
use App\Http\Controllers\CartsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\EmployeeAttendancesController;
use App\Http\Controllers\EmployeePayRecordsController;
use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\InventoryLogsController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\OrderItemsController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProductionBatchesController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\RevenueReportsController;
use App\Http\Controllers\ReviewsController;
use App\Http\Controllers\SalesReportsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SuppliersController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    /*
    |--------------------------------------------------------------------------
    | Ecommerce Routes
    |--------------------------------------------------------------------------
    */

    Route::post('/cart-items/add', [CartItemsController::class, 'addToCart']);
    Route::post('/orders/checkout', [OrdersController::class, 'checkout']);
    Route::patch('/orders/{order}/status', [OrdersController::class, 'updateStatus']);

    Route::apiResource('carts', CartsController::class);
    Route::apiResource('cart-items', CartItemsController::class);
    Route::apiResource('orders', OrdersController::class);
    Route::apiResource('order-items', OrderItemsController::class);
    Route::apiResource('reviews', ReviewsController::class);
    Route::apiResource('notifications', NotificationsController::class);

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin')->group(function (): void {
        Route::post('/cacao-batches/record-roasting', [CacaoBatchesController::class, 'recordRoasting']);
        Route::post('/production-batches/record-production', [ProductionBatchesController::class, 'recordProduction']);

        Route::get('/employee-pay-records/payroll-summary', [EmployeePayRecordsController::class, 'computePayroll']);
        Route::get('/capital-records/summary', [CapitalRecordsController::class, 'summary']);
        Route::get('/revenue-reports/summary', [RevenueReportsController::class, 'summary']);

        Route::apiResource('users', UsersController::class);
        Route::apiResource('categories', CategoriesController::class);
        Route::apiResource('products', ProductsController::class);
        Route::apiResource('suppliers', SuppliersController::class);
        Route::apiResource('cacao-purchases', CacaoPurchasesController::class);
        Route::apiResource('cacao-batches', CacaoBatchesController::class);
        Route::apiResource('production-batches', ProductionBatchesController::class);
        Route::apiResource('inventory-logs', InventoryLogsController::class);
        Route::apiResource('employees', EmployeesController::class);
        Route::apiResource('employee-attendances', EmployeeAttendancesController::class);
        Route::apiResource('employee-pay-records', EmployeePayRecordsController::class);
        Route::apiResource('expenses', ExpensesController::class);
        Route::apiResource('settings', SettingsController::class);

        /*
        |--------------------------------------------------------------------------
        | Analytics And Reports
        |--------------------------------------------------------------------------
        */

        Route::apiResource('sales-reports', SalesReportsController::class);
        Route::apiResource('revenue-reports', RevenueReportsController::class);
        Route::apiResource('capital-records', CapitalRecordsController::class);
    });
});
