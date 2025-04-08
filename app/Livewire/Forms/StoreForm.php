<?php

namespace App\Livewire\Forms;

use App\Models\Store;
use Livewire\Attributes\Validate;
use Livewire\Form;

class StoreForm extends Form
{
    public ?Store $store = null;

    #[Validate(['required'])]
    public $name = '';

    #[Validate(['nullable'])]
    public $address = '';

    #[Validate(['nullable'])]
    public $logo = '';

    public function initForm()
    {
        $this->reset();
        $this->resetErrorBag();
    }

    public function setStore(Store $store)
    {
        $this->initForm();
        $this->store = $store;
        $this->name = $store->name;
        $this->address = $store->address;
        $this->logo = $store->logo;
    }

    public function save()
    {
        $this->validate();

        if (is_null($this->store)) {
            Store::create($this->except('store'));
        } else {
            $this->store->update($this->except('store'));
        }
    }
}
