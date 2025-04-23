<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all()->map(function ($product) {
            $product->image = $product->image 
                ? asset('storage/' . $product->image) 
                : null;
            return $product;
        });

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $product = Product::create($request->all());
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
        $product->update($request->only(['name', 'price']));
        return response()->json($product);
    }
}
