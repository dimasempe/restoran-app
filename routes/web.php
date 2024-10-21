<?php

use App\Models\Order;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // return view('welcome');
    $order = Order::find(2);
    return $order->sumOrderPrice();
});
