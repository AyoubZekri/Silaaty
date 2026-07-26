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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('address')->nullable();
            $table->text('supplier_products')->nullable();
            $table->text('notes')->nullable();
            $table->string('customer_sale_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'supplier_products',
                'notes',
                'customer_sale_type',
            ]);
        });
    }
};
