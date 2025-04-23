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

    #[Validate('nullable')]
    public $image = '';

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

        $imagePath = null;
        if ($this->image && is_object($this->image)) {
            $imagePath = $this->image->store('products', 'public');
        }

        if (is_null($this->product)) {
            Product::create([
                'name' => $this->name,
                'price' => $this->price,
                'image' => $imagePath,
            ]);
        } else {
            $this->product->update([
                'name' => $this->name,
                'price' => $this->price,
                'image' => $imagePath ?? $this->product->image,
            ]);
        }
    }

    public function delete()
    {
        if ($this->product) {
            $this->product->delete();
        }
    }
}
