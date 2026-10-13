<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pos_settings', function (Blueprint $table) {
            $table->id();
            $table->string('store_name')->default('RIZWEAR');
            $table->string('store_address')->nullable();
            $table->string('store_phone')->nullable();
            $table->string('cod_area')->default('Jawa');
            $table->integer('low_stock_threshold')->default(5);
            $table->decimal('minimum_order_amount', 15, 2)->default(0);
            $table->boolean('cod_enabled')->default(true);
            $table->boolean('transfer_enabled')->default(true);
            $table->decimal('cod_fee', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_settings');
    }
};
