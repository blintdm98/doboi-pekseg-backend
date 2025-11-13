<?php

namespace App\Livewire\Forms;

use App\Models\Category;
use Livewire\Attributes\Validate;
use Livewire\Form;

class CategoryForm extends Form
{
    public ?Category $category = null;

    #[Validate(['required'])]
    public $name = '';

    public function initForm()
    {
        $this->reset();
        $this->resetErrorBag();
    }

    public function setCategory(Category $category)
    {
        $this->initForm();
        $this->category = $category;
        $this->name = $category->name;
    }

    public function save()
    {
        $this->validate();

        if (is_null($this->category)) {
            Category::create([
                'name' => $this->name,
                'sort_order' => $this->getNextSortOrder(),
            ]);
        } else {
            $this->category->update([
                'name' => $this->name,
            ]);
        }

        $this->initForm();
    }

    protected function getNextSortOrder(): int
    {
        return (int) (Category::max('sort_order') ?? 0) + 1;
    }

    public function delete()
    {
        if ($this->category) {
            $this->category->delete();
        }
    }
}

