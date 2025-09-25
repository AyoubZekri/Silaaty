<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoies', function (Blueprint $table) {
            // نضيف العمود بدون unique وبدون default
            $table->uuid('uuid')->nullable()->after('id');
        });

        // نولد UUID لكل سجل موجود
        DB::table('invoies')->get()->each(function ($invoice) {
            DB::table('invoies')
                ->where('id', $invoice->id)
                ->update(['uuid' => Str::uuid()]);
        });

        // نخلي العمود not null + unique
        Schema::table('invoies', function (Blueprint $table) {
            $table->uuid('uuid')->unique()->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('invoies', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
