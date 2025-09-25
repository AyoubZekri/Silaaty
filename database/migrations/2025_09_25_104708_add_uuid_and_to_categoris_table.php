<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categoris', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->after('id');
        });


        // نولد UUID لكل سجل موجود
        DB::table('categoris')->get()->each(function ($invoice) {
            DB::table('categoris')
                ->where('id', $invoice->id)
                ->update(['uuid' => Str::uuid()]);
        });

        // نخلي العمود not null + unique
        Schema::table('categoris', function (Blueprint $table) {
            $table->uuid('uuid')->unique()->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categoris', function (Blueprint $table) {
            $table->dropcolumn("uuid");
        });
    }
};
