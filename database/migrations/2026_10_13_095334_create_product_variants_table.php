<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('size_id')->constrained()->cascadeOnDelete();
            $table->foreignId('color_id')->constrained()->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->string('barcode')->nullable()->unique();
            $table->decimal('price', 12, 2);
            $table->decimal('compare_at_price', 12, 2)->nullable();
            $table->unsignedInteger('stock_on_hand')->default(0);
            $table->unsignedInteger('stock_reserved')->default(0);
            $table->unsignedInteger('low_stock_threshold')->default(5);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['product_id', 'size_id', 'color_id']);
            $table->index(['product_id', 'is_active']);
            $table->index(['stock_on_hand', 'stock_reserved']);
        });

        // Check constraints via raw SQL
        DB::statement('ALTER TABLE product_variants ADD CONSTRAINT check_stock_on_hand_nonnegative CHECK (stock_on_hand >= 0)');
        DB::statement('ALTER TABLE product_variants ADD CONSTRAINT check_stock_reserved_valid CHECK (stock_reserved >= 0 AND stock_reserved <= stock_on_hand)');
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
