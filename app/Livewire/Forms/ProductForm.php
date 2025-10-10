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
    public $pendingImageDelete = false;

    #[Validate(['required'])]
    public $name = '';

    #[Validate(['required', 'numeric', 'min:0', 'regex:/^\d+(\.\d{1,2})?$/'])]
    public $price = '';

    #[Validate(['required', 'in:11,21'])]
    public $tva = 11;

    #[Validate(['required', 'in:kg,db'])]
    public $unit = 'db';

    #[Validate(['nullable', 'numeric', 'min:0', 'regex:/^\d+(\.\d{1,2})?$/'])]
    public $unit_value = null;

    #[Validate(['nullable'])]
    public $accounting_code = '';

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
        $this->tva = $product->tva;
        $this->unit = $product->unit;
        $this->unit_value = $product->unit_value;
        $this->accounting_code = $product->accounting_code;
    }

    public function save()
    {
        $this->validate();

        if (is_null($this->product)) {
            $product = Product::create([
                'name' => $this->name,
                'price' => $this->price,
                'tva' => $this->tva,
                'unit' => $this->unit,
                'unit_value' => $this->unit === 'kg' ? $this->unit_value : null,
                'accounting_code' => $this->accounting_code,
            ]);
        } else {
            $this->product->update([
                'name' => $this->name,
                'price' => $this->price,
                'tva' => $this->tva,
                'unit' => $this->unit,
                'unit_value' => $this->unit === 'kg' ? $this->unit_value : null,
                'accounting_code' => $this->accounting_code,
            ]);

            $product = $this->product;
        }

        if ($this->pendingImageDelete && !$this->image) {
            $product->clearMediaCollection('images');
        }

        if ($this->image) {
            $product->clearMediaCollection('images');
            $product->addMedia($this->image->getRealPath())
                    ->usingFileName($this->image->getClientOriginalName())
                    ->toMediaCollection('images');
        }

        $this->initForm();
    }

    public function updatedImage()
    {
        $this->pendingImageDelete = false;
    }

    public function delete()
    {
        if ($this->product) {
            $this->product->delete();
        }
    }
}
