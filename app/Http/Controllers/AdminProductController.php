<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStoreRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('admin.products', compact('products'));
    }

    public function edit(Product $product)
    {
        return view('admin.products_edit', [
            'product' => $product
        ]);
    }

    public function update(ProductStoreRequest $request, Product $product)
    {
        $input = $request->validated();
        if (!empty($input['cover']) && $input['cover']->isValid()) {
            $file = $input['cover'];
            $path = $file->store('public/products');
            $input['cover'] = $path;
        }

        $product->fill($input);
        $product->save();
        return Redirect::route('admin.product');
    }

    public function create()
    {
        return view('admin.products_create');
    }
    public function store(ProductStoreRequest $request)
    {
        $input = $request->validated();

        $input['slug'] = Str::slug($input['name']);
        if (!empty($input['cover']) && $input['cover']->isValid()) {
            $file = $input['cover'];
            $path = $file->store('public/products');
            $input['cover'] = $path;
        }
        Product::create($input);

        return Redirect::route('admin.product');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        Storage::delete($product->cover ?? '');
        return Redirect::route('admin.product');
    }
}
