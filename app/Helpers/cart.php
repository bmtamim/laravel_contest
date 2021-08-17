<?php

use App\Models\Cart;

if (!function_exists('cartCount')) {

    function cartCount(): int
    {
        return Cart::query()->where('user_id', auth()->id())->get()->sum('item_quantity');
    }

}
if (!function_exists('cartTotal')) {
    function cartTotal(): float
    {
        return Cart::query()->where('user_id', auth()->id())->get()->sum('item_price');
    }
}

if (!function_exists('cartItems')) {
    function cartItems()
    {
        return Cart::query()->where('user_id', auth()->id())->get();
    }
}
