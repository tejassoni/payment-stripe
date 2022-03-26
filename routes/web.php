<?php

use Illuminate\Support\Facades\Route;


// load stripe payment page
Route::get('checkout',[App\Http\Controllers\CheckoutController::class,'viewCheckout']);

// submit stripe payment and update payment
Route::post('submitcheckout',[App\Http\Controllers\CheckoutController::class,'afterpayment']);
