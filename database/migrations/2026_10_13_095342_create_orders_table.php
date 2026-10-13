<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('channel', ['online', 'pos'])->default('online');
            $table->string('order_status')->default('pending_payment');
            $table->char('currency', 3)->default('IDR');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount_total', 12, 2)->default(0);
            $table->decimal('shipping_total', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2);

            // Snapshot alamat (tidak FK ke addresses)
            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->text('recipient_address');
            $table->string('recipient_city');
            $table->string('recipient_province');
            $table->string('recipient_postal_code');

            $table->string('idempotency_key')->nullable()->unique();
            $table->timestamps();

            $table->index(['user_id', 'order_status']);
            $table->index('order_status');
            $table->index('idempotency_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
