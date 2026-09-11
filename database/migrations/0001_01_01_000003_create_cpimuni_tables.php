<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('municipalidades')) {
            return;
        }

        Schema::create('municipalidades', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_entidad', 20)->nullable()->index();
            $table->string('ruc', 20)->nullable()->unique();
            $table->string('nombre');
            $table->enum('tipo', ['PROVINCIAL', 'DISTRITAL'])->default('DISTRITAL');
            $table->string('departamento', 100)->nullable();
            $table->string('provincia', 100)->nullable();
            $table->string('distrito', 100)->nullable();
            $table->string('logo', 500)->nullable();
            $table->enum('estado', ['ACTIVA', 'INACTIVA'])->default('ACTIVA');
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('permisos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150)->unique();
            $table->string('modulo', 100)->nullable();
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('rol_permiso', function (Blueprint $table) {
            $table->foreignId('rol_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('permiso_id')->constrained('permisos')->cascadeOnDelete();
            $table->primary(['rol_id', 'permiso_id']);
        });

        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('municipalidad_id')->nullable()->constrained('municipalidades')->nullOnDelete();
            $table->foreignId('rol_id')->constrained('roles');
            $table->string('nombre', 150);
            $table->string('apellido', 150)->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('estado', ['ACTIVO', 'INACTIVO', 'BLOQUEADO'])->default('ACTIVO');
            $table->dateTime('ultimo_acceso')->nullable();
            $table->timestamps();
        });

        Schema::create('organos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('municipalidad_id')->constrained('municipalidades')->cascadeOnDelete();
            $table->string('codigo', 50)->nullable();
            $table->string('nombre');
            $table->string('tipo', 100)->nullable();
            $table->integer('nivel_jerarquico')->nullable();
            $table->foreignId('organo_padre_id')->nullable()->constrained('organos')->nullOnDelete();
            $table->enum('estado', ['ACTIVO', 'INACTIVO'])->default('ACTIVO');
            $table->timestamps();
            $table->index(['municipalidad_id', 'codigo']);
        });

        Schema::create('unidades_organicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('municipalidad_id')->constrained('municipalidades')->cascadeOnDelete();
            $table->foreignId('organo_id')->nullable()->constrained('organos')->nullOnDelete();
            $table->string('codigo', 50)->nullable();
            $table->string('nombre');
            $table->string('tipo', 100)->nullable();
            $table->integer('nivel_jerarquico')->nullable();
            $table->foreignId('unidad_padre_id')->nullable()->constrained('unidades_organicas')->nullOnDelete();
            $table->text('finalidad')->nullable();
            $table->enum('estado', ['ACTIVA', 'INACTIVA'])->default('ACTIVA');
            $table->timestamps();
            $table->index(['municipalidad_id', 'codigo']);
        });

        Schema::create('puestos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('municipalidad_id')->constrained('municipalidades')->cascadeOnDelete();
            $table->foreignId('unidad_organica_id')->nullable()->constrained('unidades_organicas')->nullOnDelete();
            $table->string('codigo', 50)->nullable();
            $table->string('denominacion');
            $table->string('nivel', 100)->nullable();
            $table->text('finalidad')->nullable();
            $table->text('requisitos')->nullable();
            $table->text('competencias')->nullable();
            $table->enum('estado', ['ACTIVO', 'INACTIVO'])->default('ACTIVO');
            $table->timestamps();
            $table->index(['municipalidad_id', 'codigo']);
        });

        Schema::create('funciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('municipalidad_id')->constrained('municipalidades')->cascadeOnDelete();
            $table->foreignId('unidad_organica_id')->nullable()->constrained('unidades_organicas')->nullOnDelete();
            $table->foreignId('puesto_id')->nullable()->constrained('puestos')->nullOnDelete();
            $table->string('codigo', 50)->nullable();
            $table->text('descripcion');
            $table->string('tipo', 100)->nullable();
            $table->string('fuente', 100)->nullable();
            $table->enum('estado', ['VIGENTE', 'NO_VIGENTE', 'EN_REVISION'])->default('VIGENTE');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->timestamps();
            $table->index(['municipalidad_id', 'codigo']);
        });

        Schema::create('normas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('municipalidad_id')->nullable()->constrained('municipalidades')->cascadeOnDelete();
            $table->string('tipo', 100);
            $table->string('numero', 100)->nullable();
            $table->string('titulo', 500);
            $table->date('fecha_emision')->nullable();
            $table->date('fecha_vigencia')->nullable();
            $table->date('fecha_derogacion')->nullable();
            $table->enum('estado', ['VIGENTE', 'DEROGADA', 'EN_REVISION'])->default('VIGENTE');
            $table->string('archivo', 500)->nullable();
            $table->string('enlace', 1000)->nullable();
            $table->text('resumen')->nullable();
            $table->timestamps();
            $table->index(['municipalidad_id', 'estado']);
        });

        Schema::create('funcion_norma', function (Blueprint $table) {
            $table->foreignId('funcion_id')->constrained('funciones')->cascadeOnDelete();
            $table->foreignId('norma_id')->constrained('normas')->cascadeOnDelete();
            $table->primary(['funcion_id', 'norma_id']);
        });

        Schema::create('instrumentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('municipalidad_id')->constrained('municipalidades')->cascadeOnDelete();
            $table->string('tipo', 100);
            $table->string('codigo', 100)->nullable();
            $table->string('nombre', 500);
            $table->text('descripcion')->nullable();
            $table->enum('estado', ['BORRADOR', 'EN_REVISION', 'APROBADO', 'VIGENTE', 'DEROGADO'])->default('BORRADOR');
            $table->date('fecha_aprobacion')->nullable();
            $table->date('fecha_vigencia')->nullable();
            $table->date('fecha_derogacion')->nullable();
            $table->timestamps();
            $table->index(['municipalidad_id', 'estado']);
        });

        Schema::create('instrumento_versiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instrumento_id')->constrained('instrumentos')->cascadeOnDelete();
            $table->string('version', 30);
            $table->text('motivo')->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->dateTime('fecha_version')->useCurrent();
            $table->enum('estado', ['BORRADOR', 'EN_REVISION', 'OBSERVADO', 'APROBADO', 'VIGENTE', 'DEROGADO'])->default('BORRADOR');
            $table->string('archivo_docx', 500)->nullable();
            $table->string('archivo_pdf', 500)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->index(['instrumento_id', 'version']);
        });

        Schema::create('instrumento_secciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instrumento_version_id')->constrained('instrumento_versiones')->cascadeOnDelete();
            $table->string('codigo', 50)->nullable();
            $table->string('titulo', 500);
            $table->longText('contenido')->nullable();
            $table->integer('orden')->default(0);
            $table->timestamps();
            $table->index(['instrumento_version_id', 'orden']);
        });

        Schema::create('instrumento_relaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instrumento_origen_id')->constrained('instrumentos')->cascadeOnDelete();
            $table->foreignId('instrumento_destino_id')->constrained('instrumentos')->cascadeOnDelete();
            $table->string('tipo_relacion', 100);
            $table->text('descripcion')->nullable();
            $table->timestamps();
            $table->unique(['instrumento_origen_id', 'instrumento_destino_id', 'tipo_relacion'], 'instrumento_relacion_unica');
        });

        Schema::create('organigramas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('municipalidad_id')->constrained('municipalidades')->cascadeOnDelete();
            $table->string('nombre');
            $table->string('version', 30)->nullable();
            $table->enum('estado', ['BORRADOR', 'VIGENTE', 'DEROGADO'])->default('BORRADOR');
            $table->timestamps();
        });

        Schema::create('organigrama_nodos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organigrama_id')->constrained('organigramas')->cascadeOnDelete();
            $table->foreignId('organo_id')->nullable()->constrained('organos')->nullOnDelete();
            $table->foreignId('unidad_organica_id')->nullable()->constrained('unidades_organicas')->nullOnDelete();
            $table->foreignId('nodo_padre_id')->nullable()->constrained('organigrama_nodos')->nullOnDelete();
            $table->decimal('posicion_x', 12, 2)->nullable();
            $table->decimal('posicion_y', 12, 2)->nullable();
            $table->integer('orden')->default(0);
            $table->timestamps();
        });

        Schema::create('revisiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instrumento_version_id')->constrained('instrumento_versiones')->cascadeOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->string('tipo', 100);
            $table->enum('estado', ['PENDIENTE', 'APROBADA', 'OBSERVADA', 'RECHAZADA'])->default('PENDIENTE');
            $table->text('comentario')->nullable();
            $table->dateTime('fecha_inicio')->nullable();
            $table->dateTime('fecha_fin')->nullable();
            $table->timestamps();
        });

        Schema::create('observaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('revision_id')->constrained('revisiones')->cascadeOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->text('observacion');
            $table->text('respuesta')->nullable();
            $table->enum('estado', ['PENDIENTE', 'SUBSANADA', 'NO_SUBSANADA', 'CERRADA'])->default('PENDIENTE');
            $table->timestamps();
        });

        Schema::create('aprobaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instrumento_version_id')->constrained('instrumento_versiones')->cascadeOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->string('tipo', 100);
            $table->enum('resultado', ['APROBADO', 'RECHAZADO']);
            $table->string('documento_respaldo', 500)->nullable();
            $table->text('comentario')->nullable();
            $table->dateTime('fecha')->useCurrent();
            $table->timestamps();
        });

        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('municipalidad_id')->constrained('municipalidades')->cascadeOnDelete();
            $table->foreignId('instrumento_id')->nullable()->constrained('instrumentos')->nullOnDelete();
            $table->string('nombre', 500);
            $table->string('tipo', 100)->nullable();
            $table->string('ruta', 1000);
            $table->string('hash_archivo', 128)->nullable();
            $table->unsignedBigInteger('tamano')->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('auditoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('municipalidad_id')->nullable()->constrained('municipalidades')->nullOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->string('tabla', 150);
            $table->unsignedBigInteger('registro_id')->nullable();
            $table->enum('accion', ['CREAR', 'ACTUALIZAR', 'ELIMINAR', 'LOGIN', 'LOGOUT', 'EXPORTAR', 'APROBAR', 'OBSERVAR']);
            $table->text('descripcion')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 1000)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['municipalidad_id', 'created_at']);
            $table->index(['tabla', 'registro_id']);
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        foreach ([
            'auditoria', 'documentos', 'aprobaciones', 'observaciones', 'revisiones',
            'organigrama_nodos', 'organigramas', 'instrumento_relaciones', 'instrumento_secciones',
            'instrumento_versiones', 'instrumentos', 'funcion_norma', 'normas', 'funciones',
            'puestos', 'unidades_organicas', 'organos', 'usuarios', 'rol_permiso', 'permisos',
            'roles', 'municipalidades',
        ] as $table) {
            Schema::dropIfExists($table);
        }
        Schema::enableForeignKeyConstraints();
    }
};
