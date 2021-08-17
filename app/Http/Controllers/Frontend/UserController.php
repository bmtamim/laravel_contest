<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function dashboard()
    {
        $upcomingDraws = OrderItem::query()->whereHas('order', function ($query) {
            $query->whereHas('customer', function ($query) {
                $query->where('user_id', auth()->id());
            });
        })->whereHas('contest', function ($query) {
            $query->where('is_draw', false);
        })->get();

        return view('frontend.user.dashboard', compact('upcomingDraws'));
    }


    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
