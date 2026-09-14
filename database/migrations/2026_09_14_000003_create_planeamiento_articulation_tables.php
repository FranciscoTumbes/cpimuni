<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planes_estrategicos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('municipalidad_id')->constrained('municipalidades')->cascadeOnDelete();
            $table->foreignId('instrumento_id')->nullable()->constrained('instrumentos')->nullOnDelete();
            $table->string('codigo', 100)->nullable();
            $table->string('nombre', 500);
            $table->unsignedSmallInteger('anio_inicio');
            $table->unsignedSmallInteger('anio_fin');
            $table->enum('estado', ['BORRADOR', 'VIGENTE', 'CERRADO'])->default('BORRADOR');
            $table->timestamps();
            $table->unique(['municipalidad_id', 'codigo']);
            $table->index(['municipalidad_id', 'estado']);
        });

        Schema::create('objetivos_estrategicos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('plan_estrategico_id')->constrained('planes_estrategicos')->cascadeOnDelete();
            $table->foreignId('unidad_responsable_id')->nullable()->constrained('unidades_organicas')->nullOnDelete();
            $table->string('codigo', 50);
            $table->string('enunciado', 1000);
            $table->text('descripcion')->nullable();
            $table->unsignedInteger('orden')->default(0);
            $table->enum('estado', ['ACTIVO', 'INACTIVO'])->default('ACTIVO');
            $table->timestamps();
            $table->unique(['plan_estrategico_id', 'codigo']);
        });

        Schema::create('acciones_estrategicas', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('objetivo_estrategico_id')->constrained('objetivos_estrategicos')->cascadeOnDelete();
            $table->foreignId('unidad_responsable_id')->nullable()->constrained('unidades_organicas')->nullOnDelete();
            $table->string('codigo', 50);
            $table->string('enunciado', 1000);
            $table->text('descripcion')->nullable();
            $table->unsignedInteger('orden')->default(0);
            $table->enum('estado', ['ACTIVO', 'INACTIVO'])->default('ACTIVO');
            $table->timestamps();
            $table->unique(['objetivo_estrategico_id', 'codigo']);
        });

        Schema::create('planes_operativos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('municipalidad_id')->constrained('municipalidades')->cascadeOnDelete();
            $table->foreignId('instrumento_id')->nullable()->constrained('instrumentos')->nullOnDelete();
            $table->string('codigo', 100)->nullable();
            $table->string('nombre', 500);
            $table->unsignedSmallInteger('anio');
            $table->enum('estado', ['BORRADOR', 'APROBADO', 'VIGENTE', 'CERRADO'])->default('BORRADOR');
            $table->timestamps();
            $table->unique(['municipalidad_id', 'anio']);
            $table->index(['municipalidad_id', 'estado']);
        });

        Schema::create('actividades_operativas', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('plan_operativo_id')->constrained('planes_operativos')->cascadeOnDelete();
            $table->foreignId('accion_estrategica_id')->nullable()->constrained('acciones_estrategicas')->nullOnDelete();
            $table->foreignId('unidad_responsable_id')->nullable()->constrained('unidades_organicas')->nullOnDelete();
            $table->foreignId('puesto_responsable_id')->nullable()->constrained('puestos')->nullOnDelete();
            $table->string('codigo', 100);
            $table->string('denominacion', 1000);
            $table->text('descripcion')->nullable();
            $table->string('unidad_medida', 150)->nullable();
            $table->decimal('meta_programada', 18, 4)->nullable();
            $table->decimal('meta_ejecutada', 18, 4)->default(0);
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->enum('estado', ['PROGRAMADA', 'EN_EJECUCION', 'CONCLUIDA', 'CANCELADA'])->default('PROGRAMADA');
            $table->timestamps();
            $table->unique(['plan_operativo_id', 'codigo']);
            $table->index(['unidad_responsable_id', 'estado']);
        });

        Schema::create('indicadores', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('municipalidad_id')->constrained('municipalidades')->cascadeOnDelete();
            $table->foreignId('objetivo_estrategico_id')->nullable()->constrained('objetivos_estrategicos')->cascadeOnDelete();
            $table->foreignId('accion_estrategica_id')->nullable()->constrained('acciones_estrategicas')->cascadeOnDelete();
            $table->foreignId('actividad_operativa_id')->nullable()->constrained('actividades_operativas')->cascadeOnDelete();
            $table->string('codigo', 100);
            $table->string('nombre', 500);
            $table->text('formula')->nullable();
            $table->string('unidad_medida', 150)->nullable();
            $table->string('fuente', 500)->nullable();
            $table->enum('frecuencia', ['MENSUAL', 'TRIMESTRAL', 'SEMESTRAL', 'ANUAL'])->default('ANUAL');
            $table->decimal('linea_base', 18, 4)->nullable();
            $table->decimal('meta', 18, 4)->nullable();
            $table->enum('estado', ['ACTIVO', 'INACTIVO'])->default('ACTIVO');
            $table->timestamps();
            $table->unique(['municipalidad_id', 'codigo']);
            $table->index(['municipalidad_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indicadores');
        Schema::dropIfExists('actividades_operativas');
        Schema::dropIfExists('planes_operativos');
        Schema::dropIfExists('acciones_estrategicas');
        Schema::dropIfExists('objetivos_estrategicos');
        Schema::dropIfExists('planes_estrategicos');
    }
};