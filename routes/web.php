<?php

use App\Http\Controllers\CategoryProductController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionHistoryController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::redirect('/','login');

Route::get('product-link/{id}',[ProductController::class,'productLink'])->name('product.link');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('category-product',CategoryProductController::class)->except(['create','show']);

    Route::resource('product',ProductController::class)->except(['create','show']);

    Route::get('transaction',[TransactionController::class,'index'])->name('transaction.index');
    Route::post('transaction/search-product',[TransactionController::class,'searchProduct'])->name('transaction.search_product');
    Route::post('transaction/pay',[TransactionController::class,'pay'])->name('transaction.pay');



    Route::get('transaction/history',[TransactionHistoryController::class,'index'])->name('transaction.history');
    Route::get('transaction/print',[TransactionHistoryController::class,'print'])->name('transaction.print');
    Route::get('transaction-detail/{transaction_id}',[TransactionHistoryController::class,'transactionDetail'])->name('transaction.detail');

});

require __DIR__.'/auth.php';
