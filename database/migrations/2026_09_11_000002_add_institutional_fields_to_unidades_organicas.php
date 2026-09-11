<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unidades_organicas', function (Blueprint $table): void {
            $table->string('abreviatura', 30)->nullable()->after('nombre');
            $table->text('descripcion')->nullable()->after('finalidad');
            $table->string('categoria_institucional', 50)->nullable()->after('tipo');
            $table->unsignedInteger('orden')->default(0)->after('nivel_jerarquico');
            $table->index(['municipalidad_id', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::table('unidades_organicas', function (Blueprint $table): void {
            $table->dropIndex(['municipalidad_id', 'orden']);
            $table->dropColumn(['abreviatura', 'descripcion', 'categoria_institucional', 'orden']);
        });
    }
};
