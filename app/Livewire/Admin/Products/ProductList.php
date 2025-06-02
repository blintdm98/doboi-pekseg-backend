<?php

namespace App\Livewire\Admin\Products;

use App\Livewire\Forms\ProductForm;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;
use Illuminate\Support\Facades\Http;
use Livewire\WithFileUploads; 

class ProductList extends Component
{
    use WithPagination, WireUiActions, WithFileUploads;

    public ProductForm $form;

    public $productModal = false;

    public $search = '';

    public function getProducts()
    {
        $search = $this->search;

        return Product::with('media')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->latest()
            ->paginate(20);
    }

    public function openModal()
    {
        $this->form->initForm();
        $this->productModal = true;
    }

    public function editProduct(Product $product)
    {
        $this->form->setProduct($product);
        $this->productModal = true;
    }

    public function save()
    {
        $this->form->price = str_replace(',', '.', $this->form->price);
        
        $this->form->save();
        $this->productModal = false;

        $this->notification()->send([
            'icon'  => 'success',
            'title' => __('common.saved_successfully'),
        ]);
    }

    public function render()
    {
        return view('livewire.admin.products.product-list', [
            'products' => $this->getProducts(),
        ]);
    }

    public function delete()
    {
        $this->form->delete();

        $this->productModal = false;

        $this->notification()->send([
            'icon'  => 'success',
            'title' => __('common.deleted_successfully'),
        ]);
    }
}
