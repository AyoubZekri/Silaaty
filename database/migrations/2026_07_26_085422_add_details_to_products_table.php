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
        Schema::table('products', function (Blueprint $table) {
            $table->string('product_code')->nullable();
            $table->integer('quantity_per_carton')->nullable();
            $table->integer('items_per_carton')->nullable();
            $table->decimal('tva', 8, 2)->nullable();
            $table->decimal('min_selling_price', 10, 2)->nullable();
            $table->decimal('sale_discount', 8, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'product_code',
                'quantity_per_carton',
                'items_per_carton',
                'tva',
                'min_selling_price',
                'sale_discount',
            ]);
        });
    }
};
