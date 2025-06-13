<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Livewire\Form;
use Illuminate\Support\Facades\Hash;

class UserForm extends Form
{
    public ?User $user = null;

    public $name = '';
    public $user_name = '';
    public $email = '';
    public $password = '';
    public $role = 'mobil';
    public $phone = '';

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'user_name' => 'required|string|max:255|unique:users,user_name,' . optional($this->user)->id,
            'email' => 'nullable|email|unique:users,email,' . optional($this->user)->id,
            'password' => $this->user ? 'nullable|min:6' : 'required|min:6',
            'role' => 'required|in:admin,mobil',
            'phone' => 'nullable|string|min:10',
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
        $this->user_name = $user->user_name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->phone = $user->phone;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'user_name' => $this->user_name,
            'email' => $this->email,
            'role' => $this->role,
            'phone' => $this->phone,
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
