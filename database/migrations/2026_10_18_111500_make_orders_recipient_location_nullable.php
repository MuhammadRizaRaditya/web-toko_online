<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('recipient_city')->nullable()->change();
            $table->string('recipient_province')->nullable()->change();
            $table->string('recipient_postal_code')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('recipient_city')->change();
            $table->string('recipient_province')->change();
            $table->string('recipient_postal_code')->change();
        });
    }
};
