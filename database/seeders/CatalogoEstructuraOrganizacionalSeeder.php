<?php

namespace Database\Seeders;

use App\Models\CatalogoEstructuraOrganizacional;
use Illuminate\Database\Seeder;

class CatalogoEstructuraOrganizacionalSeeder extends Seeder
{
    public function run(): void
    {
        $entries = [
            ['01', null, 'Gobierno', 'NATURALEZA', 'NATURALEZA', 'Naturaleza organizacional de gobierno.', true],
            ['01.01', '01', 'Concejo Municipal', 'GOBIERNO', 'ORGANO', null, false],
            ['01.02', '01', 'Alcaldía', 'GOBIERNO', 'ORGANO', null, false],
            ['02', null, 'Alta Dirección', 'NATURALEZA', 'NATURALEZA', 'Naturaleza organizacional de alta dirección.', true],
            ['02.01', '02', 'Gerencia Municipal', 'ALTA_DIRECCION', 'GERENCIA_MUNICIPAL', null, true],
            ['03', null, 'Control', 'NATURALEZA', 'NATURALEZA', 'Naturaleza organizacional de control.', true],
            ['03.01', '03', 'Órgano de Control Institucional', 'CONTROL', 'ORGANO_CONTROL', null, false],
            ['04', null, 'Defensa Jurídica', 'NATURALEZA', 'NATURALEZA', 'Naturaleza organizacional de defensa jurídica.', true],
            ['04.01', '04', 'Procuraduría Pública Municipal', 'DEFENSA_JURIDICA', 'ORGANO_DEFENSA_JURIDICA', null, false],
            ['05', null, 'Asesoramiento', 'NATURALEZA', 'NATURALEZA', 'Naturaleza organizacional de asesoramiento.', true],
            ['05.01', '05', 'Gerencia de Asesoría Legal', 'ASESORAMIENTO', 'GERENCIA', null, true],
            ['05.02', '05', 'Gerencia de Planeamiento y Presupuesto', 'ASESORAMIENTO', 'GERENCIA', null, true],
            ['05.02.01', '05.02', 'Subgerencia de Presupuesto', 'ASESORAMIENTO', 'SUBGERENCIA', null, true],
            ['05.02.02', '05.02', 'Subgerencia de Programación e Inversión', 'ASESORAMIENTO', 'SUBGERENCIA', null, true],
            ['05.02.03', '05.02', 'Subgerencia de Racionalización y Modernización', 'ASESORAMIENTO', 'SUBGERENCIA', null, true],
            ['05.02.04', '05.02', 'Subgerencia de Formulación de Proyectos', 'ASESORAMIENTO', 'SUBGERENCIA', null, true],
            ['06', null, 'Apoyo', 'NATURALEZA', 'NATURALEZA', 'Naturaleza organizacional de apoyo.', true],
            ['06.01', '06', 'Secretaría General', 'APOYO', 'SECRETARIA_GENERAL', null, true],
            ['07', null, 'Órganos de Línea', 'NATURALEZA', 'NATURALEZA', 'Naturaleza organizacional de línea.', true],
            ['07.01', '07', 'Gerencia de Desarrollo Social y Servicios Públicos', 'LINEA', 'GERENCIA', null, true],
            ['07.01.01', '07.01', 'Subgerencia de DEMUNA, OMAPED y CIAM', 'LINEA', 'SUBGERENCIA', null, true],
            ['07.01.02', '07.01', 'Subgerencia de Educación, Cultura y Deporte', 'LINEA', 'SUBGERENCIA', null, true],
            ['07.01.03', '07.01', 'Subgerencia de Registro Civil', 'LINEA', 'SUBGERENCIA', null, true],
            ['07.01.04', '07.01', 'Subgerencia de Transporte', 'LINEA', 'SUBGERENCIA', null, true],
            ['07.01.05', '07.01', 'Subgerencia de Seguridad Ciudadana', 'LINEA', 'SUBGERENCIA', null, true],
            ['07.01.06', '07.01', 'Subgerencia de Riesgos y Desastres', 'LINEA', 'SUBGERENCIA', null, true],
            ['07.02', '07', 'Gerencia de Desarrollo Económico', 'LINEA', 'GERENCIA', null, true],
            ['07.02.01', '07.02', 'Subgerencia de Turismo', 'LINEA', 'SUBGERENCIA', null, true],
            ['07.02.02', '07.02', 'Subgerencia de Sanidad Animal y Vegetal', 'LINEA', 'SUBGERENCIA', null, true],
            ['07.02.03', '07.02', 'Subgerencia de Comercialización', 'LINEA', 'SUBGERENCIA', null, true],
            ['07.03', '07', 'Gerencia de Medio Ambiente y Salud Pública', 'LINEA', 'GERENCIA', null, true],
            ['07.03.01', '07.03', 'Subgerencia de Limpieza Pública y Áreas Verdes', 'LINEA', 'SUBGERENCIA', null, true],
            ['07.03.02', '07.03', 'Subgerencia de Gestión Ambiental Municipal', 'LINEA', 'SUBGERENCIA', null, true],
            ['07.03.03', '07.03', 'Subgerencia del Área Técnica Municipal', 'LINEA', 'SUBGERENCIA', null, true],
            ['07.04', '07', 'Gerencia de Obras y Desarrollo Urbano', 'LINEA', 'GERENCIA', null, true],
            ['07.04.01', '07.04', 'Subgerencia de Infraestructura y Obras', 'LINEA', 'SUBGERENCIA', null, true],
            ['07.04.02', '07.04', 'Subgerencia de Supervisión, Liquidación y Transferencia', 'LINEA', 'SUBGERENCIA', null, true],
            ['07.04.03', '07.04', 'Subgerencia de Estudios Técnicos', 'LINEA', 'SUBGERENCIA', null, true],
            ['07.04.04', '07.04', 'Subgerencia de Catastro y AA.HH.', 'LINEA', 'SUBGERENCIA', null, true],
            ['07.05', '07', 'Gerencia de Administración Tributaria y Rentas', 'LINEA', 'GERENCIA', null, true],
            ['07.05.01', '07.05', 'Subgerencia de Determinación y Fiscalización Tributaria', 'LINEA', 'SUBGERENCIA', null, true],
            ['07.05.02', '07.05', 'Subgerencia de Ejecución Coactiva Tributaria', 'LINEA', 'SUBGERENCIA', null, true],
            ['08', null, 'Coordinación', 'NATURALEZA', 'NATURALEZA', 'Instancias de coordinación institucional.', true],
            ['08.01', '08', 'Consejo de Coordinación Local Distrital', 'COORDINACION', 'CONSEJO', null, false],
            ['08.02', '08', 'Comité de Administración del Vaso de Leche', 'COORDINACION', 'COMITE', null, false],
            ['08.03', '08', 'Comité Distrital Ambiental Municipal', 'COORDINACION', 'COMITE', null, false],
            ['08.04', '08', 'Comité Distrital de Gestión de Riesgos y Desastres', 'COORDINACION', 'COMITE', null, false],
            ['08.05', '08', 'Junta de Delegados Vecinales', 'COORDINACION', 'JUNTA', null, false],
            ['09', null, 'Consultivo', 'NATURALEZA', 'NATURALEZA', 'Instancias consultivas institucionales.', true],
            ['09.01', '09', 'Comisión de Regidores', 'CONSULTIVO', 'COMISION', null, false],
        ];

        foreach ($entries as $order => [$code, $parentCode, $name, $category, $type, $description, $allowsChildren]) {
            CatalogoEstructuraOrganizacional::updateOrCreate(
                ['codigo' => $code],
                [
                    'codigo_padre' => $parentCode,
                    'parent_id' => $parentCode
                        ? CatalogoEstructuraOrganizacional::where('codigo', $parentCode)->value('id')
                        : null,
                    'nombre' => $name,
                    'categoria' => $category,
                    'tipo' => $type,
                    'nivel' => substr_count($code, '.') + 1,
                    'descripcion' => $description,
                    'permite_hijos' => $allowsChildren,
                    'orden' => $order + 1,
                    'estado' => 'ACTIVO',
                ]
            );
        }
    }
}