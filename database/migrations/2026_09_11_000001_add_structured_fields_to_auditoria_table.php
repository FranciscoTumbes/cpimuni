<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('auditoria', function (Blueprint $table): void {
            $table->json('datos_anteriores')->nullable()->after('descripcion');
            $table->json('datos_nuevos')->nullable()->after('datos_anteriores');
            $table->string('resultado', 30)->default('EXITOSO')->after('datos_nuevos');
            $table->string('motivo', 500)->nullable()->after('resultado');
        });
    }

    public function down(): void
    {
        Schema::table('auditoria', function (Blueprint $table): void {
            $table->dropColumn(['datos_anteriores', 'datos_nuevos', 'resultado', 'motivo']);
        });
    }
};
