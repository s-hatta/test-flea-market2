<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\CommentController;

Route::middleware('guest')->group(function () {
    Route::get('register',[RegisterController::class,'create']);
    Route::post('register',[RegisterController::class,'store']);
    Route::get('login',[LoginController::class,'create'])->name('login');
    Route::post('login',[LoginController::class,'store']);
});
Route::post('logout',[LoginController::class,'destroy']);
Route::get('/', [ItemController::class, 'index']);
Route::post('/', [ItemController::class, 'index']);
Route::get('/item/{id}', [ItemController::class, 'show'])->name('items.show');
Route::middleware('auth', 'verified')->group(function () {
    Route::post('/item/{id}/toggle-like', [ItemController::class, 'toggleLike'])->name('item.toggleLike');
    Route::get('sell', [ItemController::class, 'sell']);
    Route::post('sell', [ItemController::class, 'update'])->name('item.update');
	Route::post('/comments/{id}', [CommentController::class, 'store'])->name('comments.store');
    Route::get('/purchase/{id}', [PurchaseController::class, 'index']);
    Route::post('/purchase/{id}', [PurchaseController::class, 'execute']);
    Route::get('/purchase/address/{id}', [PurchaseController::class, 'edit']);
    Route::post('/purchase/address/{id}', [PurchaseController::class, 'update']);
    Route::get('/mypage', [UserController::class, 'index']);
    Route::get('/mypage/profile', [UserController::class, 'edit']);
    Route::post('/mypage/profile', [UserController::class, 'update'])->name('profile.update');
});
Route::get('/email/verify/{id}/{hash}', [UserController::class, 'verify'])->name('verification.verify');
