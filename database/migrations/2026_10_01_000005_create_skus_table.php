<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')
                ->constrained('variants')
                ->restrictOnDelete();
            $table->string('sku_code', 64)->unique();
            // DECIMAL, not float — required so money never suffers
            // floating-point rounding error.
            $table->decimal('price', 10, 2)->unsigned();
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Belt-and-suspenders: DB-level CHECK where the engine supports it
        // (MySQL 8.0.16+). The real, version-independent guarantee against
        // negative stock is the conditional atomic update in Sku::decrementStock().
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE skus ADD CONSTRAINT chk_skus_stock_non_negative CHECK (stock_quantity >= 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('skus');
    }
};
