<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVariantRequest;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Validation\ValidationException;

class VariantController extends Controller
{
    public function store(StoreVariantRequest $request, Product $product)
    {
        $optionValues = $request->validated()['option_values'];
        $signature = Variant::signatureFor($optionValues);

        $exists = Variant::where('product_id', $product->id)
            ->where('option_signature', $signature)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'option_values' => 'This product already has a variant with this exact combination.',
            ]);
        }

        $variant = Variant::create([
            'product_id' => $product->id,
            'option_values' => $optionValues,
            'option_signature' => $signature,
        ]);

        return response()->json(['data' => $variant], 201);
    }
}
