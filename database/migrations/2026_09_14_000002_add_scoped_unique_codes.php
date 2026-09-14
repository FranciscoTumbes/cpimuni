<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organos', function (Blueprint $table): void {
            $table->unique(['municipalidad_id', 'codigo'], 'organos_municipalidad_codigo_unique');
        });
        Schema::table('unidades_organicas', function (Blueprint $table): void {
            $table->unique(['municipalidad_id', 'codigo'], 'unidades_municipalidad_codigo_unique');
        });
        Schema::table('puestos', function (Blueprint $table): void {
            $table->unique(['municipalidad_id', 'codigo'], 'puestos_municipalidad_codigo_unique');
        });
        Schema::table('funciones', function (Blueprint $table): void {
            $table->unique(['municipalidad_id', 'codigo'], 'funciones_municipalidad_codigo_unique');
        });
    }

    public function down(): void
    {
        Schema::table('funciones', fn (Blueprint $table) => $table->dropUnique('funciones_municipalidad_codigo_unique'));
        Schema::table('puestos', fn (Blueprint $table) => $table->dropUnique('puestos_municipalidad_codigo_unique'));
        Schema::table('unidades_organicas', fn (Blueprint $table) => $table->dropUnique('unidades_municipalidad_codigo_unique'));
        Schema::table('organos', fn (Blueprint $table) => $table->dropUnique('organos_municipalidad_codigo_unique'));
    }
};