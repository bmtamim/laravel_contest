<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserLogin extends Component
{
    public $login_error;
    public $login_email;
    public $login_pass;
    public $login_remember = false;

    protected $rules = [
        'login_email' => ['required', 'string', 'email'],
        'login_pass'  => ['required', 'string'],
    ];

    public function render()
    {
        return view('livewire.user-login');
    }

    public function login()
    {

        $this->validate();

        if (Auth::attempt(['email' => $this->login_email, 'password' => $this->login_pass], $this->login_remember)) {
            $this->login_error = null;
            return redirect()->route('home');
        }
        $this->login_error = 'The provided credentials do not match our records.';
    }
}
