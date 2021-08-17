<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Winner;
use Illuminate\Http\Request;

class WinnerController extends Controller
{

    public function index()
    {
        $winners = Winner::query()->with(['order', 'order.customer' => function ($query) {
            $query->with(['user']);
        }, 'contest'])->latest()->get();

        return view('frontend.core.winners', compact('winners'));
    }
}
