<?php

namespace App\Livewire\Admin\Products;

use App\Livewire\Forms\ProductForm;
use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use WireUi\Traits\WireUiActions;
use Livewire\WithFileUploads; 

class ProductList extends Component
{
    use WireUiActions, WithFileUploads;

    public ProductForm $form;
    public $productModal = false;
    public $search = '';

    public function getProducts()
    {
        $search = $this->search;

        return Product::with(['media', 'categories'])
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('price', 'like', '%' . $search . '%')
                  ->orWhereHas('categories', function ($categoryQuery) use ($search) {
                      $categoryQuery->where('name', 'like', '%' . $search . '%');
                  });
            });
        })
        ->orderBy('sort_order')
        ->orderByDesc('id')
        ->get();
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
        
        if ($this->form->unit_value) {
            $this->form->unit_value = str_replace(',', '.', $this->form->unit_value);
        }
        
        $this->form->save();
        $this->productModal = false;

        $this->notification()->send([
            'icon'  => 'success',
            'title' => __('common.saved_successfully'),
        ]);
    }

    public function render()
    {
        $categories = Category::orderBy('sort_order')->orderBy('id')->get()
            ->map(fn($category) => [
                'value' => $category->id,
                'label' => $category->name,
            ])->toArray();

        return view('livewire.admin.products.product-list', [
            'products' => $this->getProducts(),
            'categories' => $categories,
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

    public function deleteImage()
    {
        if ($this->form->product) {
            $this->form->product->clearMediaCollection('images');
            $this->form->product->refresh();
        }
    }

    public function updateProductOrder(array $order): void
    {
        foreach ($order as $item) {
            Product::whereKey($item['value'])
                ->update(['sort_order' => $item['order']]);
        }

        $this->notification()->send([
            'icon'  => 'success',
            'title' => __('common.order_updated'),
        ]);
    }
}
