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

    public $search = '';

    public $roles = [
        'admin' => 'Admin',
        'mobil' => 'Mobil',
    ];

    public function openModal()
    {
        $this->form->initForm();
        $this->userModal = true;
        $this->resetErrorBag();
    }

    public function editUser(User $user)
    {
        $this->form->setUser($user);
        $this->userModal = true;
        $this->form->password = null;
        $this->resetErrorBag();
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
        return User::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(20);
    }

    public function render()
    {
        return view('livewire.admin.users.user-list', [
            'users' => $this->getUsers(),
        ]);
    }

    public function delete()
    {
        $this->form->delete();

        $this->userModal = false;

        $this->notification()->send([
            'icon'  => 'success',
            'title' => __('common.deleted_successfully'),
        ]);
    }
}
