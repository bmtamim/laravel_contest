<?php

namespace App\Http\Livewire;

use App\Models\Winner;
use Livewire\Component;

class Winners extends Component
{
    public $winners;

    public $contest_no;
    public $ticket_no_1;
    public $ticket_no_2;
    public $ticket_no_3;
    public $ticket_no_4;
    public $ticket_no_5;
    public $ticket_no_6;
    public $ticket_no_7;

    protected $rules = [
        'contest_no'  => ['required'],
        'ticket_no_1' => ['required'],
        'ticket_no_2' => ['required'],
        'ticket_no_3' => ['required'],
        'ticket_no_4' => ['required'],
        'ticket_no_5' => ['required'],
        'ticket_no_6' => ['required'],
        'ticket_no_7' => ['required'],
    ];


    public function render()
    {
        return view('livewire.winners');
    }

    public function ticketCheck()
    {
        $this->validate();
        $ticket_no = [
            $this->ticket_no_1,
            $this->ticket_no_2,
            $this->ticket_no_3,
            $this->ticket_no_4,
            $this->ticket_no_5,
            $this->ticket_no_6,
            $this->ticket_no_7,
        ];
        $winner = Winner::query()->where(['contest_no' => $this->contest_no, 'ticket_no' => serialize($ticket_no)])->with(['order', 'order.customer' => function ($query) {
            $query->with(['user']);
        }, 'contest'])->latest()->get();

        $this->winners = $winner;
    }
}
