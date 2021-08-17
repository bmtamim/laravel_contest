<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index()
    {
        $cartItems = Cart::query()->where('user_id', auth()->id())->get();
        return view('frontend.core.checkout', compact('cartItems'));
    }

    public function checkoutSuccess($order_id)
    {
        return $order = Order::query()->with(['orderItems','orderPayment','customer'])->first();
    }
}
