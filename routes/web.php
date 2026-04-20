<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminFeedbackController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminSupportController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FamilyMemberController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\ShoppingListController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'landing'])->middleware('guest');
Route::get('/register', [AuthController::class, 'showRegister'])->middleware('guest')->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
Route::get('/login', [AuthController::class, 'showLogin'])->middleware('guest')->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [ScanController::class, 'dashboard']);

    Route::get('/profile', [UserController::class, 'index']);
    Route::get('/profile/edit', [UserController::class, 'edit']);
    Route::post('/profile', [UserController::class, 'update']);
    Route::get('/profile/password', [UserController::class, 'editPassword']);
    Route::post('/profile/password', [UserController::class, 'updatePassword']);
    Route::get('/explore', [ProductController::class, 'explore']);
    Route::get('/products/{id}', [ProductController::class, 'show']);

    Route::get('/family', [FamilyMemberController::class, 'index']);
    Route::post('/family', [FamilyMemberController::class, 'store']);
    Route::get('/family/{id}/edit', [FamilyMemberController::class, 'edit']);
    Route::put('/family/{id}', [FamilyMemberController::class, 'update']);
    Route::delete('/family/{id}', [FamilyMemberController::class, 'destroy']);

    Route::post('/scan/member', [ScanController::class, 'selectMember']);
    Route::get('/scan', [ScanController::class, 'index']);
    Route::post('/scan/barcode', [ScanController::class, 'scan']);
    Route::get('/scan/history', [ScanController::class, 'history']);
    Route::delete('/scan/history/{id}', [ScanController::class, 'deleteRecord']);
    Route::post('/scan/rescan/{id}', [ScanController::class, 'rescan']);

    Route::get('/budget/{memberId}', [BudgetController::class, 'edit']);
    Route::put('/budget/{memberId}', [BudgetController::class, 'update']);

    Route::get('/cart', [CartController::class, 'index']);
    Route::get('/cart/payment', [CartController::class, 'payment']);
    Route::post('/cart/payment/done', [CartController::class, 'paymentDone']);
    Route::post('/cart', [CartController::class, 'addItem']);
    Route::delete('/cart/clear', [CartController::class, 'clearCart']);
    Route::put('/cart/{id}', [CartController::class, 'updateQty']);
    Route::delete('/cart/{id}', [CartController::class, 'removeItem']);
    Route::post('/cart/checkout', [CartController::class, 'checkout']);

    Route::get('/list', [ShoppingListController::class, 'index']);
    Route::post('/list', [ShoppingListController::class, 'store']);
    Route::patch('/list/{id}/toggle', [ShoppingListController::class, 'toggle']);
    Route::delete('/list/{id}', [ShoppingListController::class, 'destroy']);

    Route::get('/support', [SupportController::class, 'index']);
    Route::get('/support/tickets', [SupportController::class, 'tickets']);
    Route::get('/support/feedback', [FeedbackController::class, 'index']);
    Route::post('/feedback', [FeedbackController::class, 'store']);
    Route::delete('/feedback/{id}', [FeedbackController::class, 'destroy']);
    Route::post('/support', [SupportController::class, 'store']);
    Route::delete('/support/{id}', [SupportController::class, 'destroy']);
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index']);

    Route::get('/profile/edit', [AdminProfileController::class, 'edit']);
    Route::post('/profile', [AdminProfileController::class, 'update']);
    Route::get('/profile/password', [AdminProfileController::class, 'editPassword']);
    Route::post('/profile/password', [AdminProfileController::class, 'updatePassword']);

    Route::get('/products', [AdminProductController::class, 'index']);
    Route::post('/products', [AdminProductController::class, 'store']);
    Route::put('/products/{id}', [AdminProductController::class, 'update']);
    Route::delete('/products/{id}', [AdminProductController::class, 'destroy']);

    Route::get('/users', [AdminUserController::class, 'index']);
    Route::put('/users/{id}', [AdminUserController::class, 'update']);
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy']);

    Route::get('/feedback', [AdminFeedbackController::class, 'index']);
    Route::delete('/feedback/{id}', [AdminFeedbackController::class, 'destroy']);

    Route::get('/support', [AdminSupportController::class, 'index']);
    Route::put('/support/{id}', [AdminSupportController::class, 'update']);
    Route::delete('/support/{id}', [AdminSupportController::class, 'destroy']);
});
