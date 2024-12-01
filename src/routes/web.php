<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\CommentController;

Route::get('register',[RegisterController::class,'create']);
Route::post('register',[RegisterController::class,'store']);
Route::get('login',[LoginController::class,'create'])->name('login');
Route::post('login',[LoginController::class,'store']);
Route::post('logout',[LoginController::class,'destroy']);
Route::get('/', [ItemController::class, 'index']);
Route::get('/item/{id}', [ItemController::class, 'show'])->name('items.show');
Route::middleware('auth')->group(function () {
    Route::get('sell', [ItemController::class, 'sell']);
	Route::post('/comments/{id}', [CommentController::class, 'store'])->name('comments.store');
    Route::get('/purchase/{id}', [PurchaseController::class, 'index']);
    Route::get('/purchase/address/{id}', [PurchaseController::class, 'edit']);
    Route::get('/mypage', [UserController::class, 'index']);
    Route::get('/mypage/profile', [UserController::class, 'edit']);
});