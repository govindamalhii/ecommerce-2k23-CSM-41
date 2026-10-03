<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();
            $table->json('option_values');
            // Canonical hash of sorted option_values — see Variant::signatureFor().
            // Paired with product_id in a unique index so the same combination
            // can never be created twice for one product (CAT04).
            $table->string('option_signature', 191);
            $table->timestamps();

            $table->unique(['product_id', 'option_signature']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variants');
    }
};
