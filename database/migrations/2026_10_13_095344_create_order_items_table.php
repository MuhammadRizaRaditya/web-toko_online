<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->string('product_name_snapshot');
            $table->string('sku_snapshot');
            $table->string('size_snapshot')->nullable();
            $table->string('color_snapshot')->nullable();
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('line_total', 12, 2);
            $table->timestamps();
        });

        DB::statement('ALTER TABLE order_items ADD CONSTRAINT check_quantity_positive CHECK (quantity > 0)');
        DB::statement('ALTER TABLE order_items ADD CONSTRAINT check_unit_price_nonnegative CHECK (unit_price >= 0)');
        DB::statement('ALTER TABLE order_items ADD CONSTRAINT check_discount_amount_nonnegative CHECK (discount_amount >= 0)');
        DB::statement('ALTER TABLE order_items ADD CONSTRAINT check_line_total_nonnegative CHECK (line_total >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
