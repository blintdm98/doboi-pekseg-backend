<?php

namespace App\Livewire\Admin\Users;

use App\Livewire\Forms\UserForm;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class UserList extends Component
{
    use WithPagination, WireUiActions;

    public UserForm $form;

    public $userModal = false;

    public function openModal()
    {
        $this->form->initForm();
        $this->userModal = true;
    }

    public function editUser(User $user)
    {
        $this->form->setUser($user);
        $this->userModal = true;
    }

    public function save()
    {
        $this->form->save();
        $this->userModal = false;

        $this->notification()->send([
            'icon'  => 'success',
            'title' => __('common.saved_successfully'),
        ]);
    }

    public function getUsers()
    {
        return User::latest()->paginate(20);
    }

    public function render()
    {
        return view('livewire.admin.users.user-list', [
            'users' => $this->getUsers(),
        ]);
    }
}
