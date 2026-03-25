<?php

declare(strict_types=1);

use App\Domain\Cart\v1\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:sanctum'], static function () {
    Route::post('purchase', [CartController::class, 'purchase'])->name('cart.purchase');
});
