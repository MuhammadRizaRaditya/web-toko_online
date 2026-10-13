<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->unsignedInteger('quantity_ordered');
            $table->unsignedInteger('quantity_received')->default(0);
            $table->decimal('unit_cost', 12, 2);
            $table->timestamps();
        });

        DB::statement('ALTER TABLE purchase_order_items ADD CONSTRAINT check_quantity_ordered_nonnegative CHECK (quantity_ordered >= 0)');
        DB::statement('ALTER TABLE purchase_order_items ADD CONSTRAINT check_quantity_received_nonnegative CHECK (quantity_received >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
