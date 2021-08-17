<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Contest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Winner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::query()->with(['orderItems', 'orderPayment', 'customer'])->latest('id')->get();
        return view('backend.orders.index', compact('orders'));
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }


    public function edit($id)
    {
        $order = Order::query()->with(['orderItems', 'orderItems.contest', 'orderPayment', 'customer'])->findOrFail($id);
        return view('backend.orders.edit', compact('order'));
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        try {
            $order->delete();

            Session::flash('toast', [
                'type' => 'success',
                'msg'  => 'Success, Order Deleted!!',
            ]);

        } catch (\Exception $e) {
            Session::flash('toast', [
                'type' => 'error',
                'msg'  => $e->getMessage(),
            ]);
        }

        return back();
    }

    public function makeWinner($order_id, Request $request)
    {
        $request->validate([
            'win_amount' => ['required', 'numeric'],
            'order_item' => ['required'],
        ]);

        $orderItem = OrderItem::query()->whereHas('order', function ($q) use ($order_id) {
            $q->where('id', $order_id);
        })->findOrFail($request->order_item);

        try {
            if ($orderItem) {
                Winner::create([
                    'order_id'   => $orderItem->order->id,
                    'contest_id' => $orderItem->contest_id,
                    'contest_no' => $orderItem->contest_no,
                    'ticket_no'  => $orderItem->item_value,
                    'amount'     => $request->win_amount,
                ]);

                $contest = Contest::findOrFail($orderItem->contest_id);

                $contest->update([
                    'is_draw'   => true,
                    'draw_date' => Carbon::now(),
                ]);

                Contest::where('id', $contest->contest_id)->update([
                    'is_draw'   => true,
                    'draw_date' => Carbon::now(),
                ]);
            }

            Session::flash('toast', [
                'type' => 'success',
                'msg'  => 'Success, Winner Selected!!',
            ]);
        } catch (\Exception $e) {
            Session::flash('toast', [
                'type' => 'error',
                'msg'  => $e->getMessage(),
            ]);
        }

        return back();

    }
}
