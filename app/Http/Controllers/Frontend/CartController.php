<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::query()->where('user_id', auth()->id())->get();
        return view('frontend.core.cart', compact('cartItems'));
    }

    public function removeCart($id): RedirectResponse
    {
        $cart = Cart::query()->where('user_id', auth()->id())->findOrFail($id);
        $cart->delete();
        return back();
    }
}
