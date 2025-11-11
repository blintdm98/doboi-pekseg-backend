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
    public $pendingLogoDelete = false;

    #[Validate(['required'])]
    public $name = '';

    #[Validate(['nullable'])]
    public $address = '';

    #[Validate(['nullable', 'image', 'max:2048'])]
    public $logo = '';

    #[Validate(['nullable', 'string', 'min:10'])]
    public $phone = '';

    #[Validate(['nullable'])]
    public $contact_person = '';

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
        $this->phone = $store->phone;
        $this->contact_person = $store->contact_person;
    }

    public function save()
    {
        $this->validate();

        if (is_null($this->store)) {
            $store = Store::create([
                'name' => $this->name,
                'address' => $this->address,
                'phone' => $this->phone,
                'contact_person' => $this->contact_person,
                'sort_order' => $this->getNextSortOrder(),
            ]);
        } else {
            $this->store->update([
                'name' => $this->name,
                'address' => $this->address,
                'phone' => $this->phone,
                'contact_person' => $this->contact_person,
            ]);
            $store = $this->store;
        }

        if ($this->pendingLogoDelete && !$this->logo) {
            $store->clearMediaCollection('logos');
        }

        if ($this->logo) {
            $store->clearMediaCollection('logos');
            $store->addMedia($this->logo->getRealPath())
                ->usingFileName($this->logo->getClientOriginalName())
                ->toMediaCollection('logos');
        }

        $this->initForm();
    }

    protected function getNextSortOrder(): int
    {
        return (int) (Store::max('sort_order') ?? 0) + 1;
    }

    public function delete()
    {
        if ($this->store) {
            $this->store->delete();
        }
    }
}
