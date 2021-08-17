<?php

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class UserRegistration extends Component
{
    public $signup_name;
    public $signup_email;
    public $signup_password;
    public $signup_re_pass;

    protected $rules = [
        'signup_name'     => ['required', 'string'],
        'signup_email'    => ['required', 'string', 'email', 'unique:users,email'],
        'signup_password' => ['required', 'string', 'min:3'],
        'signup_re_pass'  => ['required', 'string', 'min:3', 'same:signup_password'],
    ];

    public function render()
    {
        return view('livewire.user-registration');
    }

    public function registration()
    {
        $this->validate();

        $user = User::create([
            'name'     => $this->signup_name,
            'email'    => $this->signup_email,
            'password' => Hash::make($this->signup_password),
        ]);

        event(new Registered($user));

        Auth::attempt(['email' => $user->email, 'password' => $this->signup_password], true);

        return redirect()->route('home');

    }
}
