<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function index()
    {
        return Product::with('category', 'variants.skus')->paginate(20);
    }

    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated() + ['status' => 'draft']);

        return response()->json(['data' => $product], 201);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();

        if (($data['status'] ?? null) === 'published' && ! $product->canPublish()) {
            throw ValidationException::withMessages([
                'status' => 'A product needs at least one active SKU before it can be published.',
            ]);
        }

        $product->update($data);

        return response()->json(['data' => $product->fresh(['variants.skus'])]);
    }
}
