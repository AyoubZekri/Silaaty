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
        Schema::create('zakats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal("zakat_nisab",13,2)->default(0.00);
            $table->decimal("zakat_total_asset_value",13,2)->default(0.00);
            $table->decimal("zakat_due_amount",3,1)->default("2.5");
            $table->decimal("zakat_due",13,2)->default(0.00);
            $table->decimal("zakat_total_debts_value", 13, 2)->default(0.00);// الديون
            $table->decimal("zakat_total_deborts_value", 13, 2)->default(0.00); // بكريدي
            $table->decimal("zakat_Cash_liquidity", 13, 2)->default(0.00);// الديون
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zakats');

    }
};
