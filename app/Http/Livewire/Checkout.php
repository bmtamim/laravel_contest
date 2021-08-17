<?php

namespace App\Http\Livewire;

use App\Models\Cart;
use App\Models\Contest;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Checkout extends Component
{
    public $cartItems;

    public $name;
    public $email;
    public $phone;

    public $stripe_card;
    public $stripe_expiry;
    public $stripe_cvc;

    public bool $checkout_success = false;

    protected $rules = [
        'name'          => ['required', 'string'],
        'email'         => ['required', 'string', 'email'],
        'phone'         => ['required', 'string'],
        'stripe_card'   => ['nullable', 'string'],
        'stripe_expiry' => ['nullable', 'string'],
        'stripe_cvc'    => ['nullable', 'string'],
    ];


    public function render()
    {
        return view('livewire.checkout');
    }

    public function checkout()
    {
        $this->validate();
        if (cartTotal() <= 0.00) {
            return redirect()->route('contests.index');
        }
        $order = DB::transaction(function () {
            $customer = Customer::query()->where('user_id', auth()->id())->first();
            if (!$customer) {
                $customer = Customer::create([
                    'user_id' => auth()->id(),
                    'name'    => $this->name,
                    'email'   => $this->email,
                    'phone'   => $this->phone,
                ]);
            } else {
                $customer->update([
                    'name'  => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                ]);
            }
            //Create Order
            $order = Order::create([
                'customer_id'    => $customer->id,
                'total_price'    => cartTotal(),
                'total_quantity' => cartCount(),
            ]);

            $order->orderPayment()->create([
                'method' => 'stripe',
                'amount' => cartTotal(),
            ]);

            $cartItems = Cart::query()->where('user_id', auth()->id())->get();
            foreach ($cartItems as $cartItem) {
                $contest = Contest::query()->where('contest_id', $cartItem->contest_id)->first();

                if (!$contest) {
                    $parentContest = Contest::query()->find($cartItem->contest_id);
                    $contest = $parentContest->replicate();
                    $contest->contest_id = $parentContest->id;
                    $contest->save();
                }

                $order->orderItems()->create([
                    'contest_id'    => $contest->id,
                    'contest_no'    => $cartItem->contest_no,
                    'item_price'    => $cartItem->item_price,
                    'item_quantity' => $cartItem->item_quantity,
                    'item_value'    => $cartItem->item_value,
                ]);
                $cartItem->delete();
            }
            return $order;
        });

        $this->checkout_success = true;

        return redirect()->route('checkout.success', $order->id);
    }

}


