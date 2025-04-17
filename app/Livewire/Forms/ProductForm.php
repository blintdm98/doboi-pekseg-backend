<?php

namespace App\Livewire\Forms;

use App\Models\Product;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ProductForm extends Form
{
    public ?Product $product = null;

    #[Validate(['required'])]
    public $name = '';

    #[Validate(['required', 'numeric'])]
    public $price = '';

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
            Product::create($this->except('product'));
        } else {
            $this->product->update($this->except('product'));
        }
    }

    public function delete()
    {
        if ($this->product) {
            $this->product->delete();
        }
    }
}
