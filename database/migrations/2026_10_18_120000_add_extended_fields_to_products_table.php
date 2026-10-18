<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('brand', 100)->nullable()->after('slug');
            $table->string('material', 100)->nullable()->after('brand');
            $table->string('fit_style', 100)->nullable()->after('material');
            $table->unsignedInteger('weight_grams')->nullable()->after('fit_style');
            $table->text('size_chart')->nullable()->after('weight_grams');
            $table->json('labels')->nullable()->after('description');
            $table->boolean('is_featured')->default(false)->after('status');
            $table->decimal('cost_price', 12, 2)->nullable()->after('status');

            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_featured']);
            $table->dropColumn([
                'brand',
                'material',
                'fit_style',
                'weight_grams',
                'size_chart',
                'labels',
                'is_featured',
                'cost_price',
            ]);
        });
    }
};
