<?php

namespace App\Livewire\Admin\Store;

use App\Livewire\Forms\StoreForm;
use App\Models\Store;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;
use Livewire\WithFileUploads;

class StoreList extends Component
{
    use WithPagination, WireUiActions, WithFileUploads;

    public StoreForm $form;

    public $storeModal = false;

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
        return Store::latest()
            ->paginate(20);
    }

    public function render()
    {
        return view('livewire.admin.store.store-list', [
            'stores' => $this->getStores()
        ]);
    }
}
