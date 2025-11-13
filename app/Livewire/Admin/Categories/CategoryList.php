<?php

namespace App\Livewire\Admin\Categories;

use App\Livewire\Forms\CategoryForm;
use App\Models\Category;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class CategoryList extends Component
{
    use WireUiActions;

    public CategoryForm $form;

    public $categoryModal = false;

    public $search = '';

    public function openModal()
    {
        $this->form->initForm();
        $this->categoryModal = true;
    }

    public function editCategory(Category $category)
    {
        $this->form->setCategory($category);
        $this->categoryModal = true;
    }

    public function save()
    {
        $this->form->save();
        $this->categoryModal = false;

        $this->notification()->send([
            'icon'  => 'success',
            'title' => __('common.saved_successfully'),
        ]);
    }

    public function getCategories()
    {
        return Category::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.categories.category-list', [
            'categories' => $this->getCategories(),
        ]);
    }

    public function delete()
    {
        $this->form->delete();

        $this->categoryModal = false;

        $this->notification()->send([
            'icon'  => 'success',
            'title' => __('common.deleted_successfully'),
        ]);
    }

    public function updateCategoryOrder(array $order): void
    {
        foreach ($order as $item) {
            Category::whereKey($item['value'])
                ->update(['sort_order' => $item['order']]);
        }

        $this->notification()->send([
            'icon'  => 'success',
            'title' => __('common.order_updated'),
        ]);
    }
}
