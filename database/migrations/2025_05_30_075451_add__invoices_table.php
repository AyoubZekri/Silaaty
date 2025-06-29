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
        Schema::create('invoies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('Transaction_id')->constrained('Transactions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string("invoies_numper");
            $table->date("invoies_date");
            $table->date("invoies_payment_date");
            $table->integer("Payment_price")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoies');

    }
};
