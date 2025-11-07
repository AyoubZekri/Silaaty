<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->tinyInteger("type_sales")->nullable();
        });

        DB::table('sales')
            ->join('invoies', 'sales.invoie_id', '=', 'invoies.id')
            ->join('Transactions', 'invoies.Transaction_id', '=', 'Transactions.id')
            ->update([
                'sales.type_sales' => DB::raw('Transactions.transactions')
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn("type_sales");
        });
    }
};
