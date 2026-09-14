<?php

namespace App\Services;

use App\Models\Funcion;
use App\Models\Instrumento;
use App\Models\Municipalidad;
use App\Models\Norma;
use App\Models\Organo;
use App\Models\Puesto;
use App\Models\UnidadOrganica;
use Illuminate\Support\Facades\DB;

class MunicipalidadCloneService
{
    public function clone(Municipalidad $source, array $attributes, array $options): Municipalidad
    {
        return DB::transaction(function () use ($source, $attributes, $options): Municipalidad {
            $destination = Municipalidad::create($attributes + ['estado' => 'ACTIVA']);
            $organoMap = [];
            $unidadMap = [];
            $puestoMap = [];
            $normaMap = [];

            if ($options['estructura'] ?? false) {
                foreach ($source->organos()->orderBy('nivel_jerarquico')->orderBy('id')->get() as $organo) {
                    $copy = $organo->replicate();
                    $copy->municipalidad_id = $destination->id;
                    $copy->organo_padre_id = $organo->organo_padre_id ? $organoMap[$organo->organo_padre_id] : null;
                    $copy->save();
                    $organoMap[$organo->id] = $copy->id;
                }

                foreach ($source->unidadesOrganicas()->orderBy('nivel_jerarquico')->orderBy('id')->get() as $unidad) {
                    $copy = $unidad->replicate();
                    $copy->municipalidad_id = $destination->id;
                    $copy->organo_id = $unidad->organo_id ? $organoMap[$unidad->organo_id] : null;
                    $copy->unidad_padre_id = $unidad->unidad_padre_id ? $unidadMap[$unidad->unidad_padre_id] : null;
                    $copy->save();
                    $unidadMap[$unidad->id] = $copy->id;
                }
            }

            if (($options['puestos'] ?? false) && $unidadMap) {
                foreach ($source->puestos()->orderBy('id')->get() as $puesto) {
                    if (!$puesto->unidad_organica_id || !isset($unidadMap[$puesto->unidad_organica_id])) continue;
                    $copy = $puesto->replicate();
                    $copy->municipalidad_id = $destination->id;
                    $copy->unidad_organica_id = $unidadMap[$puesto->unidad_organica_id];
                    $copy->save();
                    $puestoMap[$puesto->id] = $copy->id;
                }
            }

            if (($options['funciones'] ?? false) && $unidadMap) {
                foreach ($source->funciones()->orderBy('id')->get() as $funcion) {
                    if (!$funcion->unidad_organica_id || !isset($unidadMap[$funcion->unidad_organica_id])) continue;
                    $copy = $funcion->replicate();
                    $copy->municipalidad_id = $destination->id;
                    $copy->unidad_organica_id = $unidadMap[$funcion->unidad_organica_id];
                    $copy->puesto_id = $funcion->puesto_id && isset($puestoMap[$funcion->puesto_id])
                        ? $puestoMap[$funcion->puesto_id]
                        : null;
                    $copy->save();
                }
            }

            if ($options['normativa'] ?? false) {
                foreach ($source->normas()->orderBy('id')->get() as $norma) {
                    $copy = $norma->replicate();
                    $copy->municipalidad_id = $destination->id;
                    $copy->save();
                    $normaMap[$norma->id] = $copy->id;
                }

                foreach ($source->funciones()->with('normas')->get() as $funcion) {
                    if (!isset($unidadMap[$funcion->unidad_organica_id])) continue;
                    $newFuncion = Funcion::where('municipalidad_id', $destination->id)
                        ->where('codigo', $funcion->codigo)
                        ->where('unidad_organica_id', $unidadMap[$funcion->unidad_organica_id])
                        ->first();
                    if (!$newFuncion) continue;
                    $newFuncion->normas()->sync($funcion->normas->map(fn (Norma $item) => $normaMap[$item->id] ?? null)->filter()->all());
                }
            }

            if ($options['instrumentos'] ?? false) {
                foreach ($source->instrumentos()->orderBy('id')->get() as $instrumento) {
                    $copy = $instrumento->replicate();
                    $copy->municipalidad_id = $destination->id;
                    $copy->save();
                }
            }

            return $destination;
        });
    }
}