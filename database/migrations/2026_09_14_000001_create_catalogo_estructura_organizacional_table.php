<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalogo_estructura_organizacional', function (Blueprint $table): void {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('codigo_padre', 50)->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('catalogo_estructura_organizacional')->nullOnDelete();
            $table->string('nombre');
            $table->string('categoria', 50);
            $table->string('tipo', 100);
            $table->unsignedInteger('nivel');
            $table->text('descripcion')->nullable();
            $table->boolean('permite_hijos')->default(true);
            $table->unsignedInteger('orden')->default(0);
            $table->enum('estado', ['ACTIVO', 'INACTIVO'])->default('ACTIVO');
            $table->timestamps();

            $table->index(['categoria', 'tipo']);
            $table->index(['parent_id', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalogo_estructura_organizacional');
    }
};