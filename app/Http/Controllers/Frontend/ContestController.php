<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Contest;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ContestController extends Controller
{
    public function index(Request $request):View
    {
        $min_price = ltrim($request->input('min_amount'), '$');
        $max_price = ltrim($request->input('max_amount'), '$');

        /*$test = Contest::query()->when($request->has('query'), function ($query) use ($request) {
            $query->where('title', 'LIKE', '%' . $request->query('query') . '%')
                ->orWhere('slug', 'LIKE', '%' . $request->query('query') . '%')
                ->orWhere('contest_no', 'LIKE', '%' . $request->query('query') . '%');
        })->when($request->has('min_amount') && $request->has('max_amount'), function ($query) use ($min_price, $max_price) {
            $query->whereBetween('ticket_price', [$min_price, $max_price]);
        })->get();*/

        //===========================================//
        $builder = Contest::query();
        if ($request->query('query') && !empty($request->query('query'))) {
            $query = $request->query('query');
            $builder->where('title', 'LIKE', '%' . $query . '%')
                ->orWhere('slug', 'LIKE', '%' . $query . '%')
                ->orWhere('contest_no', 'LIKE', '%' . $query . '%');
        }

        if ($request->has('min_amount') && $request->has('max_amount')) {
            $min_price = ltrim($request->input('min_amount'), '$');
            $max_price = ltrim($request->input('max_amount'), '$');
            $builder->whereBetween('ticket_price', [$min_price, $max_price]);
        }
        $builder->where(['is_draw' => false])->active()->parent()->latest()->get();
        $contests = $builder->latest()->get();

        $priceMin = Contest::query()->active()->parent()->min('ticket_price');
        $priceMax = Contest::query()->active()->parent()->max('ticket_price');
        $priceRange = [
            $priceMin,
            $priceMax
        ];

        return view('frontend.contest.all-contests', compact('contests', 'priceRange'));
    }

    public function singleContest($slug):View
    {
        $contest = Contest::query()->active()->parent()->where(['slug' => $slug, 'is_draw' => false])->with('contest_gallery')->firstOrFail();

        $buying_ability = true;
        if (auth()->check() && auth()->user()->customer) {

            $orderItems = OrderItem::query()->whereHas('order', function ($query) {
                $query->where('customer_id', auth()->user()->customer->id);
            })->pluck('contest_id')->toArray();

            $orderedContest = Contest::query()->whereIn('id', $orderItems)->pluck('contest_id')->toArray();

            if (in_array($contest->id, $orderedContest) && !$contest->is_draw) {
                $buying_ability = false;
            }
        }
        return view('frontend.contest.single-contest', compact('contest', 'buying_ability'));
    }

    public function contestTicketsCreate($contest_id): View
    {
        $contest = Contest::query()->active()->parent()->findOrFail($contest_id);
        return view('frontend.contest.contest-ticket', compact('contest'));
    }

    public function contestTicketsStore($contest_id, Request $request): RedirectResponse
    {
        $request->validate([
            'ticket_number'   => ['required', 'array'],
            'ticket_number.*' => ['integer'],
        ], [
            'ticket_number.*.integer' => 'Choose your 6 numbers ( 5 numbers + 1 Lucky number).',
        ]);

        if (!Auth::check()) {
            Session::flash('cartMsg', 'Sorry, You are not logged in!');
            return back();
        }

        $contest = Contest::query()->active()->parent()->where(['is_draw' => false])->findOrFail($contest_id);

        if (auth()->user()->customer) {
            $orderItems = OrderItem::query()->whereHas('order', function ($query) {
                $query->where('customer_id', auth()->user()->customer->id);
            })->pluck('contest_id')->toArray();

            $orderedContest = Contest::query()->whereIn('id', $orderItems)->pluck('contest_id')->toArray();

            if (in_array($contest->id, $orderedContest) && !$contest->is_draw) {
                Session::flash('cartMsg', 'You already purchased a ticket of this contest!');
                return back();
            }
        }

        $tickets = serialize($request->ticket_number);

        $checkCart = Cart::query()->where(['user_id' => auth()->id(), 'contest_id' => $contest->id])->first();

        if (!$checkCart) {
            Cart::create([
                'user_id'       => Auth::id(),
                'contest_id'    => $contest->id,
                'contest_no'    => $contest->contest_no,
                'item_price'    => $contest->ticket_price,
                'item_quantity' => 1,
                'item_value'    => $tickets,
            ]);

            $contest->update(['ticket_sold' => ($contest->ticket_sold + 1)]);

            Session::flash('cartMsg', 'Contest added to Cart!');

        } else {
            Session::flash('cartMsg', 'Contest already has on Cart!');
        }

        return back();
    }

}
