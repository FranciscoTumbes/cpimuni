<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organos', function (Blueprint $table): void {
            if (!Schema::hasColumn('organos', 'naturaleza')) {
                $table->string('naturaleza', 100)->nullable()->after('nombre');
            }
        });

        Schema::table('unidades_organicas', function (Blueprint $table): void {
            if (!Schema::hasColumn('unidades_organicas', 'naturaleza')) {
                $table->string('naturaleza', 100)->nullable()->after('nombre');
            }
        });
    }

    public function down(): void
    {
        Schema::table('organos', function (Blueprint $table): void {
            if (Schema::hasColumn('organos', 'naturaleza')) {
                $table->dropColumn('naturaleza');
            }
        });

        Schema::table('unidades_organicas', function (Blueprint $table): void {
            if (Schema::hasColumn('unidades_organicas', 'naturaleza')) {
                $table->dropColumn('naturaleza');
            }
        });
    }
};
