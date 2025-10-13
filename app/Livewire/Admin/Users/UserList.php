<?php

namespace App\Livewire\Admin\Users;

use App\Livewire\Forms\UserForm;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class UserList extends Component
{
    use WithPagination, WireUiActions;

    public UserForm $form;

    public bool $userModal = false;

    public ?string $search = null;

    public bool $showStoresModal = false;

    public ?int $storesUserId = null;

    public array $storeSelection = [];

    public bool $selectAllStores = false;

    public $roles = [
        'admin' => 'Admin',
        'mobil' => 'Mobil',
    ];

    public function openModal(): void
    {
        $this->form->initForm();
        $this->userModal = true;
        $this->resetErrorBag();
    }

    public function editUser(User $user): void
    {
        $this->form->setUser($user);
        $this->userModal = true;
        $this->form->password = null;
        $this->resetErrorBag();
    }

    public function save(): void
    {
        $this->form->save();
        $this->userModal = false;

        $this->notification()->send([
            'icon'  => 'success',
            'title' => __('common.saved_successfully'),
        ]);
        $this->resetPage();
    }

    public function getUsers(): LengthAwarePaginator
    {
        return User::query()
            ->when($this->search, function ($query): void {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('phone', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            })
            ->latest()
            ->paginate(20);
    }

    public function render(): View
    {
        return view('livewire.admin.users.user-list', [
            'users' => $this->getUsers(),
        ]);
    }

    public function delete(): void
    {
        $this->form->delete();

        $this->userModal = false;

        $this->notification()->send([
            'icon'  => 'success',
            'title' => __('common.deleted_successfully'),
        ]);
    }

    public function updated(): void
    {
        $this->gotoPage(1);
    }

    public function openStoresModal(int $userId): void
    {
        $user = User::find($userId);
        if (!$user) {
            return;
        }

        $this->storesUserId = $userId;

        $stores = \App\Models\Store::orderBy('name')->get();
        $userStoreIds = $user->stores()->pluck('stores.id')->toArray();

        $this->storeSelection = $stores->map(function ($store) use ($userStoreIds) {
            return [
                'id'              => $store->id,
                'name'            => $store->name,
                'address'         => $store->address,
                'phone'           => $store->phone,
                'contact_person'  => $store->contact_person,
                'checked'         => in_array($store->id, $userStoreIds),
            ];
        })->toArray();

        $this->selectAllStores = count($userStoreIds) === count($stores) && count($stores) > 0;
        $this->showStoresModal = true;
    }

    public function updatedSelectAllStores($value): void
    {
        foreach ($this->storeSelection as $idx => $store) {
            $this->storeSelection[$idx]['checked'] = $value;
        }
    }

    public function updatedStoreSelection(): void
    {
        $allChecked = true;
        foreach ($this->storeSelection as $store) {
            if (empty($store['checked'])) {
                $allChecked = false;
                break;
            }
        }
        $this->selectAllStores = $allChecked;
    }

    public function saveStores(): void
    {
        $userId = $this->storesUserId ?? 0;
        if ($userId === 0) {
            $this->showStoresModal = false;
            return;
        }

        $user = User::find($userId);
        if (!$user) {
            $this->showStoresModal = false;
            return;
        }

        $selectedStoreIds = collect($this->storeSelection)
            ->filter(fn ($store): bool => !empty($store['checked']))
            ->pluck('id')
            ->toArray();

        $user->stores()->sync($selectedStoreIds);

        $this->showStoresModal = false;
        $this->storesUserId = null;
        $this->storeSelection = [];
        $this->selectAllStores = false;

        $this->notification()->send([
            'icon'  => 'success',
            'title' => __('common.saved_successfully'),
        ]);
    }
}
