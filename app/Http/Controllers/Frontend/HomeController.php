<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Contest;
use App\Models\Winner;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke()
    {
        $contests = Contest::query()->where(['is_draw' => false])->active()->parent()->latest()->take(6)->get();
        $winners = Winner::query()->with(['order', 'order.customer' => function ($query) {
            $query->with(['user']);
        }, 'contest'])->latest()->get();
        return view('frontend.home', compact('contests', 'winners'));
    }
}
