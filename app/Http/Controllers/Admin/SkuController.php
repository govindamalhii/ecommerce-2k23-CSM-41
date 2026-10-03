<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSkuRequest;
use App\Http\Requests\Admin\UpdateSkuRequest;
use App\Models\Product;
use App\Models\Sku;

class SkuController extends Controller
{
    public function store(StoreSkuRequest $request, Product $product)
    {
        $data = $request->validated();

        // variant_id is confirmed to exist by the request; confirm it also
        // belongs to *this* product, not a different one.
        $variant = $product->variants()->findOrFail($data['variant_id']);

        $sku = Sku::create([
            'variant_id' => $variant->id,
            'sku_code' => $data['sku_code'],
            'price' => $data['price'],
            'stock_quantity' => $data['stock_quantity'],
        ]);

        return response()->json(['data' => $sku], 201);
    }

    public function update(UpdateSkuRequest $request, Sku $sku)
    {
        $sku->update($request->validated());

        return response()->json(['data' => $sku->fresh()]);
    }
}
