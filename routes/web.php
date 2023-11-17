<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Route::middleware(['auth'])->group(function () {
    Route::prefix('profile')->group(function () {
        Route::get('/orders', [\App\Http\Controllers\User\OrderController::class, 'index'])->name('user.order.index');
    });

    Route::prefix('products')->group(function () {
        Route::get('/', [\App\Http\Controllers\Product\ProductController::class, 'index'])->name('product.index');
        Route::post('/create', [\App\Http\Controllers\Product\ProductController::class, 'create'])->name('product.create');
    });
    Route::prefix('basket')->group(function () {
        Route::get('/', [\App\Http\Controllers\Cart\CartController::class, 'viewCart'])->name('basket.index');
        Route::post('/add/{product}/{quantity?}', [\App\Http\Controllers\Cart\CartController::class, 'addToCart'])->name('basket.add');
        Route::post('/remove/{product}', [\App\Http\Controllers\Cart\CartController::class, 'removeFromCart'])->name('basket.remove');
    });
    Route::prefix('order')->group(function () {
        Route::post('/store', [\App\Http\Controllers\Order\OrderController::class, 'store'])->name('order.store');
    });
    Route::prefix('Payment')->group(function () {
        Route::get('/Create/transaction/{order}', [\App\Http\Controllers\Payment\PaymentController::class, 'create'])->name('payment.create');
        Route::post('/Create/transaction/{order}', [\App\Http\Controllers\Payment\PaymentController::class, 'store'])->name('payment.store');

        Route::get('/prepare-to-pay/{order}/{payment}', [\App\Http\Controllers\Payment\PaymentController::class, 'prepare'])->name('payment.prepare');
        Route::post('/prepare-to-pay/{order}/{payment}', [\App\Http\Controllers\Payment\PaymentController::class, 'redirectToGateway']);

        Route::get('/callback/{gateway}', [\App\Http\Controllers\Payment\PaymentController::class, 'callback'])->name('payment.callback');
    });
});



