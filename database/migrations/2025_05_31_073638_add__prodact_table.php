<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('invoies_id')->constrained('invoies')->onDelete('cascade');
            $table->foreignId('categoris_id')->constrained('categoris')->onDelete('cascade');
            $table->string("product_name");
            $table->string("Product_image")->nullable();
            $table->string("product_description");
            $table->string("product_quantity");
            $table->decimal("product_price",13,2);
            $table->decimal("product_price_total", 13, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');

    }
};
