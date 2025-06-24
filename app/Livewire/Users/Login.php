<?php

namespace App\Livewire\Users;

use Livewire\Component;

class Login extends Component
{
    public $user_name;

    public $password;

    public $validatedField;

    protected $rules = [
        'user_name' => 'required|exists:users|max:255',
        'password'  => 'required|max:255',
    ];

    public function save()
    {
        $this->validate([
            'user_name' => 'required|exists:users|max:255',
            'password'  => 'required|max:255',
        ]);

        if (!auth()->attempt([
            'user_name' => $this->user_name,
            'password'  => $this->password,
        ])) {
            $this->addError('common', __('common.your_credentials_invalid'));
            return;
        }

        if (auth()->user()->role !== 'admin') {
            auth()->logout();
            $this->addError('common', 'Csak admin felhasználók léphetnek be.');
            return;
        }

        session()->regenerate();
        return redirect('/');
    }

    public function render()
    {
        return view('livewire.users.login');
    }

    public function updated($propertyName)
    {
        $this->resetErrorBag('common');
    }
}
