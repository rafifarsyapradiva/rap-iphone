<?php

use App\Http\Controllers\Api\MidtransCallbackController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Rap-iPhone
|--------------------------------------------------------------------------
*/

// Midtrans Payment Gateway Webhook
Route::post('/midtrans/callback', [MidtransCallbackController::class, 'handle'])
    ->name('api.midtrans.callback');

