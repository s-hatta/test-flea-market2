<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;

Route::get('/', [ItemController::class, 'index']);
Route::middleware('auth')->group(function () {
    Route::get('sell', [ItemController::class, 'sell']);
});