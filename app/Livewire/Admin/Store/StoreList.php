<?php

namespace App\Livewire\Admin\Store;

use App\Livewire\Forms\StoreForm;
use App\Models\Store;
use Livewire\Component;
use WireUi\Traits\WireUiActions;
use Livewire\WithFileUploads;

class StoreList extends Component
{
    use WireUiActions, WithFileUploads;

    public StoreForm $form;

    public $storeModal = false;

    public $search = '';

    public function openModal()
    {
        $this->form->initForm();
        $this->storeModal = true;
    }

    public function editStore(Store $store)
    {
        $this->form->setStore($store);
        $this->storeModal = true;
    }

    public function save()
    {
        $this->form->save();
        $this->storeModal = false;

        $this->notification()->send([
            'icon'  => 'success',
            'title' => __('common.saved_successfully'),
        ]);
    }

    public function getStores()
    {
        return Store::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('address', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('contact_person', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();
    }


    public function render()
    {
        return view('livewire.admin.store.store-list', [
            'stores' => $this->getStores(),
        ]);
    }

    public function delete()
    {
        $this->form->delete();

        $this->storeModal = false;

        $this->notification()->send([
            'icon'  => 'success',
            'title' => __('common.deleted_successfully'),
        ]);
    }

    public function updateStoreOrder(array $order): void
    {
        foreach ($order as $item) {
            Store::whereKey($item['value'])
                ->update(['sort_order' => $item['order']]);
        }

        $this->notification()->send([
            'icon'  => 'success',
            'title' => __('common.order_updated'),
        ]);
    }
}
