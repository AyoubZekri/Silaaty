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
        Schema::table('notifications', function (Blueprint $table) {
                $table->uuid('uuid')->unique()->after('id');
        });

        
        // نولد UUID لكل سجل موجود
        DB::table('notifications')->get()->each(function ($invoice) {
            DB::table('notifications')
                ->where('id', $invoice->id)
                ->update(['uuid' => Str::uuid()]);
        });

        // نخلي العمود not null + unique
        Schema::table('notifications', function (Blueprint $table) {
            $table->uuid('uuid')->unique()->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropcolumn("uuid");
        });
    }
};
