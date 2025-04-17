<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Livewire\Form;
use Illuminate\Support\Facades\Hash;

class UserForm extends Form
{
    public ?User $user = null;

    public $name = '';
    public $email = '';
    public $password = '';

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . optional($this->user)->id,
            'password' => $this->user ? 'nullable|min:6' : 'required|min:6',
        ];
    }

    public function initForm()
    {
        $this->reset();
        $this->user = null;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->user) {
            $this->user->update($data);
        } else {
            User::create($data);
        }
    }

    public function delete()
    {
        if ($this->user) {
            $this->user->delete();
        }
    }
}
