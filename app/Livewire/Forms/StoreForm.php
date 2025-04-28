<?php

namespace App\Livewire\Forms;

use App\Models\Store;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Livewire\WithFileUploads;

class StoreForm extends Form
{

    use WithFileUploads; 
    public ?Store $store = null;

    #[Validate(['required'])]
    public $name = '';

    #[Validate(['nullable'])]
    public $address = '';

    #[Validate(['nullable', 'image', 'max:2048'])]
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
        // $this->logo = $store->logo;
    }

    public function save()
    {
        $this->validate();

        if (is_null($this->store)) {
            $store = Store::create([
                'name' => $this->name,
                'address' => $this->address,
            ]);
        } else {
            $this->store->update([
                'name' => $this->name,
                'address' => $this->address,
            ]);
            $store = $this->store;
        }

        if ($this->logo) {
            $store->clearMediaCollection('logos');

            $store->addMedia($this->logo->getRealPath())
                ->usingFileName($this->logo->getClientOriginalName())
                ->toMediaCollection('logos');
        }
    }

    public function delete()
    {
        if ($this->store) {
            $this->store->delete();
        }
    }
}
