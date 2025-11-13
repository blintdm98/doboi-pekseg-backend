<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::query()
            ->with('categories')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => number_format((float)$product->price, 2, '.', ''),
                'tva' => $product->tva,
                'unit' => $product->unit,
                'unit_value' => $product->unit_value ? number_format((float)$product->unit_value, 2, '.', '') : null,
                'accounting_code' => $product->accounting_code,
                'image' => $product->getFirstMediaUrl('images') ?: null,
                'categories' => $product->categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                    ];
                }),
            ];
            });

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'tva' => 'nullable|in:11,21',
            'unit' => 'nullable|in:kg,db',
            'unit_value' => 'nullable|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'accounting_code' => 'nullable|string|max:255',
        ]);

        $product = Product::create($validated);
        return response()->json($product, 201);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Termék törölve'], 200);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'tva' => 'nullable|in:11,21',
            'unit' => 'nullable|in:kg,db',
            'unit_value' => 'nullable|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'accounting_code' => 'nullable|string|max:255',
        ]);

        $product->update($validated);
        return response()->json($product);
    }
}
