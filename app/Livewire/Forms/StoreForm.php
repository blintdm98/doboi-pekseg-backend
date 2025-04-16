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
        // $this->logo = $store->logo;
    }

    public function save()
    {
        $this->validate();

        $logoPath = null;

        // Ha új fájl lett kiválasztva, akkor mentsük el
        if ($this->logo && is_object($this->logo)) {
            $logoPath = $this->logo->store('logos', 'public');
        }

        if (is_null($this->store)) {
            Store::create([
                'name' => $this->name,
                'address' => $this->address,
                'logo' => $logoPath,
            ]);
        } else {
            $this->store->update([
                'name' => $this->name,
                'address' => $this->address,
                'logo' => $logoPath ?? $this->store->logo, // ha nem módosítjuk
            ]);
        }
    }
}
