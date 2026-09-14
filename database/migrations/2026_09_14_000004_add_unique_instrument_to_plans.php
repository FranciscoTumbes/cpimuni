<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('planes_estrategicos', function (Blueprint $table): void {
            $table->unique('instrumento_id', 'planes_estrategicos_instrumento_unique');
        });
        Schema::table('planes_operativos', function (Blueprint $table): void {
            $table->unique('instrumento_id', 'planes_operativos_instrumento_unique');
        });
    }

    public function down(): void
    {
        Schema::table('planes_operativos', fn (Blueprint $table) => $table->dropUnique('planes_operativos_instrumento_unique'));
        Schema::table('planes_estrategicos', fn (Blueprint $table) => $table->dropUnique('planes_estrategicos_instrumento_unique'));
    }
};