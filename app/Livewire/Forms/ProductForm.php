<?php

namespace App\Livewire\Forms;

use App\Models\Product;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Livewire\WithFileUploads;

class ProductForm extends Form
{
    use WithFileUploads; 
    public ?Product $product = null;

    #[Validate(['required'])]
    public $name = '';

    #[Validate(['required', 'numeric'])]
    public $price = '';

    #[Validate(['nullable', 'image', 'max:2048'])]
    public $image;

    public function initForm()
    {
        $this->reset();
        $this->resetErrorBag();
    }

    public function setProduct(Product $product)
    {
        $this->initForm();
        $this->product = $product;
        $this->name = $product->name;
        $this->price = $product->price;
    }

    public function save()
    {
        $this->validate();

        if (is_null($this->product)) {
            $product = Product::create([
                'name' => $this->name,
                'price' => $this->price,
            ]);
        } else {
            $this->product->update([
                'name' => $this->name,
                'price' => $this->price,
            ]);

            $product = $this->product;
        }

        if ($this->image) {
            $product->clearMediaCollection('images');

            $product->addMedia($this->image->getRealPath())
                    ->usingFileName($this->image->getClientOriginalName())
                    ->toMediaCollection('images');
        }
    }

    public function delete()
    {
        if ($this->product) {
            $this->product->delete();
        }
    }
}
