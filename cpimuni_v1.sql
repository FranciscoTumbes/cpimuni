-- ============================================================
-- CPIMUNI MULTI-MUNICIPALIDAD
-- Base de datos inicial v1.0
-- Compatible con MariaDB 10.4+
-- ============================================================

CREATE DATABASE IF NOT EXISTS cpimuni
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE cpimuni;

-- ------------------------------------------------------------
-- 1. MUNICIPALIDADES
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS municipalidades (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo_entidad VARCHAR(20) NULL,
    ruc VARCHAR(20) NULL,
    nombre VARCHAR(255) NOT NULL,
    tipo ENUM('PROVINCIAL','DISTRITAL') NOT NULL DEFAULT 'DISTRITAL',
    departamento VARCHAR(100) NULL,
    provincia VARCHAR(100) NULL,
    distrito VARCHAR(100) NULL,
    logo VARCHAR(500) NULL,
    estado ENUM('ACTIVA','INACTIVA') NOT NULL DEFAULT 'ACTIVA',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_municipalidad_ruc (ruc),
    KEY idx_municipalidad_codigo (codigo_entidad)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 2. ROLES Y PERMISOS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_roles_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS permisos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    modulo VARCHAR(100) NULL,
    descripcion VARCHAR(255) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_permisos_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS rol_permiso (
    rol_id BIGINT UNSIGNED NOT NULL,
    permiso_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (rol_id, permiso_id),
    CONSTRAINT fk_rp_rol FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE CASCADE,
    CONSTRAINT fk_rp_permiso FOREIGN KEY (permiso_id) REFERENCES permisos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 3. USUARIOS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    municipalidad_id BIGINT UNSIGNED NULL,
    rol_id BIGINT UNSIGNED NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    apellido VARCHAR(150) NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    estado ENUM('ACTIVO','INACTIVO','BLOQUEADO') NOT NULL DEFAULT 'ACTIVO',
    ultimo_acceso DATETIME NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_usuarios_email (email),
    KEY idx_usuarios_municipalidad (municipalidad_id),
    CONSTRAINT fk_usuario_municipalidad
        FOREIGN KEY (municipalidad_id) REFERENCES municipalidades(id) ON DELETE SET NULL,
    CONSTRAINT fk_usuario_rol
        FOREIGN KEY (rol_id) REFERENCES roles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 4. ESTRUCTURA ORGANIZACIONAL
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS organos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    municipalidad_id BIGINT UNSIGNED NOT NULL,
    codigo VARCHAR(50) NULL,
    nombre VARCHAR(255) NOT NULL,
    tipo VARCHAR(100) NULL,
    nivel_jerarquico INT NULL,
    organo_padre_id BIGINT UNSIGNED NULL,
    estado ENUM('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_organos_municipalidad (municipalidad_id),
    KEY idx_organos_padre (organo_padre_id),
    CONSTRAINT fk_organo_municipalidad
        FOREIGN KEY (municipalidad_id) REFERENCES municipalidades(id) ON DELETE CASCADE,
    CONSTRAINT fk_organo_padre
        FOREIGN KEY (organo_padre_id) REFERENCES organos(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS unidades_organicas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    municipalidad_id BIGINT UNSIGNED NOT NULL,
    organo_id BIGINT UNSIGNED NULL,
    codigo VARCHAR(50) NULL,
    nombre VARCHAR(255) NOT NULL,
    tipo VARCHAR(100) NULL,
    nivel_jerarquico INT NULL,
    unidad_padre_id BIGINT UNSIGNED NULL,
    finalidad TEXT NULL,
    estado ENUM('ACTIVA','INACTIVA') NOT NULL DEFAULT 'ACTIVA',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_unidades_municipalidad (municipalidad_id),
    KEY idx_unidades_organo (organo_id),
    KEY idx_unidades_padre (unidad_padre_id),
    CONSTRAINT fk_unidad_municipalidad
        FOREIGN KEY (municipalidad_id) REFERENCES municipalidades(id) ON DELETE CASCADE,
    CONSTRAINT fk_unidad_organo
        FOREIGN KEY (organo_id) REFERENCES organos(id) ON DELETE SET NULL,
    CONSTRAINT fk_unidad_padre
        FOREIGN KEY (unidad_padre_id) REFERENCES unidades_organicas(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 5. PUESTOS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS puestos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    municipalidad_id BIGINT UNSIGNED NOT NULL,
    unidad_organica_id BIGINT UNSIGNED NULL,
    codigo VARCHAR(50) NULL,
    denominacion VARCHAR(255) NOT NULL,
    nivel VARCHAR(100) NULL,
    finalidad TEXT NULL,
    requisitos TEXT NULL,
    competencias TEXT NULL,
    estado ENUM('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_puestos_municipalidad (municipalidad_id),
    KEY idx_puestos_unidad (unidad_organica_id),
    CONSTRAINT fk_puesto_municipalidad
        FOREIGN KEY (municipalidad_id) REFERENCES municipalidades(id) ON DELETE CASCADE,
    CONSTRAINT fk_puesto_unidad
        FOREIGN KEY (unidad_organica_id) REFERENCES unidades_organicas(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 6. FUNCIONES
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS funciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    municipalidad_id BIGINT UNSIGNED NOT NULL,
    unidad_organica_id BIGINT UNSIGNED NULL,
    puesto_id BIGINT UNSIGNED NULL,
    codigo VARCHAR(50) NULL,
    descripcion TEXT NOT NULL,
    tipo VARCHAR(100) NULL,
    fuente VARCHAR(100) NULL,
    estado ENUM('VIGENTE','NO_VIGENTE','EN_REVISION') NOT NULL DEFAULT 'VIGENTE',
    fecha_inicio DATE NULL,
    fecha_fin DATE NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_funciones_municipalidad (municipalidad_id),
    KEY idx_funciones_unidad (unidad_organica_id),
    KEY idx_funciones_puesto (puesto_id),
    CONSTRAINT fk_funcion_municipalidad
        FOREIGN KEY (municipalidad_id) REFERENCES municipalidades(id) ON DELETE CASCADE,
    CONSTRAINT fk_funcion_unidad
        FOREIGN KEY (unidad_organica_id) REFERENCES unidades_organicas(id) ON DELETE SET NULL,
    CONSTRAINT fk_funcion_puesto
        FOREIGN KEY (puesto_id) REFERENCES puestos(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 7. NORMATIVA
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS normas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    municipalidad_id BIGINT UNSIGNED NULL,
    tipo VARCHAR(100) NOT NULL,
    numero VARCHAR(100) NULL,
    titulo VARCHAR(500) NOT NULL,
    fecha_emision DATE NULL,
    fecha_vigencia DATE NULL,
    fecha_derogacion DATE NULL,
    estado ENUM('VIGENTE','DEROGADA','EN_REVISION') NOT NULL DEFAULT 'VIGENTE',
    archivo VARCHAR(500) NULL,
    enlace VARCHAR(1000) NULL,
    resumen TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_normas_municipalidad (municipalidad_id),
    KEY idx_normas_estado (estado),
    CONSTRAINT fk_norma_municipalidad
        FOREIGN KEY (municipalidad_id) REFERENCES municipalidades(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS funcion_norma (
    funcion_id BIGINT UNSIGNED NOT NULL,
    norma_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (funcion_id, norma_id),
    CONSTRAINT fk_fn_funcion FOREIGN KEY (funcion_id) REFERENCES funciones(id) ON DELETE CASCADE,
    CONSTRAINT fk_fn_norma FOREIGN KEY (norma_id) REFERENCES normas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 8. INSTRUMENTOS DE GESTIÓN
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS instrumentos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    municipalidad_id BIGINT UNSIGNED NOT NULL,
    tipo VARCHAR(100) NOT NULL,
    codigo VARCHAR(100) NULL,
    nombre VARCHAR(500) NOT NULL,
    descripcion TEXT NULL,
    estado ENUM('BORRADOR','EN_REVISION','APROBADO','VIGENTE','DEROGADO') NOT NULL DEFAULT 'BORRADOR',
    fecha_aprobacion DATE NULL,
    fecha_vigencia DATE NULL,
    fecha_derogacion DATE NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_instrumentos_municipalidad (municipalidad_id),
    KEY idx_instrumentos_tipo (tipo),
    KEY idx_instrumentos_estado (estado),
    CONSTRAINT fk_instrumento_municipalidad
        FOREIGN KEY (municipalidad_id) REFERENCES municipalidades(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS instrumento_versiones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    instrumento_id BIGINT UNSIGNED NOT NULL,
    version VARCHAR(30) NOT NULL,
    motivo TEXT NULL,
    usuario_id BIGINT UNSIGNED NULL,
    fecha_version DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('BORRADOR','EN_REVISION','OBSERVADO','APROBADO','VIGENTE','DEROGADO') NOT NULL DEFAULT 'BORRADOR',
    archivo_docx VARCHAR(500) NULL,
    archivo_pdf VARCHAR(500) NULL,
    observaciones TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_version_instrumento (instrumento_id),
    KEY idx_version_usuario (usuario_id),
    CONSTRAINT fk_version_instrumento
        FOREIGN KEY (instrumento_id) REFERENCES instrumentos(id) ON DELETE CASCADE,
    CONSTRAINT fk_version_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS instrumento_secciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    instrumento_version_id BIGINT UNSIGNED NOT NULL,
    codigo VARCHAR(50) NULL,
    titulo VARCHAR(500) NOT NULL,
    contenido LONGTEXT NULL,
    orden INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_seccion_version (instrumento_version_id),
    CONSTRAINT fk_seccion_version
        FOREIGN KEY (instrumento_version_id) REFERENCES instrumento_versiones(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 9. RELACIONES ENTRE INSTRUMENTOS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS instrumento_relaciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    instrumento_origen_id BIGINT UNSIGNED NOT NULL,
    instrumento_destino_id BIGINT UNSIGNED NOT NULL,
    tipo_relacion VARCHAR(100) NOT NULL,
    descripcion TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_ir_origen (instrumento_origen_id),
    KEY idx_ir_destino (instrumento_destino_id),
    CONSTRAINT fk_ir_origen
        FOREIGN KEY (instrumento_origen_id) REFERENCES instrumentos(id) ON DELETE CASCADE,
    CONSTRAINT fk_ir_destino
        FOREIGN KEY (instrumento_destino_id) REFERENCES instrumentos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 10. ORGANIGRAMA
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS organigramas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    municipalidad_id BIGINT UNSIGNED NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    version VARCHAR(30) NULL,
    estado ENUM('BORRADOR','VIGENTE','DEROGADO') NOT NULL DEFAULT 'BORRADOR',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_organigramas_municipalidad (municipalidad_id),
    CONSTRAINT fk_organigrama_municipalidad
        FOREIGN KEY (municipalidad_id) REFERENCES municipalidades(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS organigrama_nodos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organigrama_id BIGINT UNSIGNED NOT NULL,
    organo_id BIGINT UNSIGNED NULL,
    unidad_organica_id BIGINT UNSIGNED NULL,
    nodo_padre_id BIGINT UNSIGNED NULL,
    posicion_x DECIMAL(12,2) NULL,
    posicion_y DECIMAL(12,2) NULL,
    orden INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_on_organigrama (organigrama_id),
    KEY idx_on_padre (nodo_padre_id),
    CONSTRAINT fk_on_organigrama
        FOREIGN KEY (organigrama_id) REFERENCES organigramas(id) ON DELETE CASCADE,
    CONSTRAINT fk_on_organo
        FOREIGN KEY (organo_id) REFERENCES organos(id) ON DELETE SET NULL,
    CONSTRAINT fk_on_unidad
        FOREIGN KEY (unidad_organica_id) REFERENCES unidades_organicas(id) ON DELETE SET NULL,
    CONSTRAINT fk_on_padre
        FOREIGN KEY (nodo_padre_id) REFERENCES organigrama_nodos(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 11. REVISIONES, OBSERVACIONES Y APROBACIONES
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS revisiones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    instrumento_version_id BIGINT UNSIGNED NOT NULL,
    usuario_id BIGINT UNSIGNED NULL,
    tipo VARCHAR(100) NOT NULL,
    estado ENUM('PENDIENTE','APROBADA','OBSERVADA','RECHAZADA') NOT NULL DEFAULT 'PENDIENTE',
    comentario TEXT NULL,
    fecha_inicio DATETIME NULL,
    fecha_fin DATETIME NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_revision_version (instrumento_version_id),
    CONSTRAINT fk_revision_version
        FOREIGN KEY (instrumento_version_id) REFERENCES instrumento_versiones(id) ON DELETE CASCADE,
    CONSTRAINT fk_revision_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS observaciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    revision_id BIGINT UNSIGNED NOT NULL,
    usuario_id BIGINT UNSIGNED NULL,
    observacion TEXT NOT NULL,
    respuesta TEXT NULL,
    estado ENUM('PENDIENTE','SUBSANADA','NO_SUBSANADA','CERRADA') NOT NULL DEFAULT 'PENDIENTE',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_observacion_revision (revision_id),
    CONSTRAINT fk_observacion_revision
        FOREIGN KEY (revision_id) REFERENCES revisiones(id) ON DELETE CASCADE,
    CONSTRAINT fk_observacion_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS aprobaciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    instrumento_version_id BIGINT UNSIGNED NOT NULL,
    usuario_id BIGINT UNSIGNED NULL,
    tipo VARCHAR(100) NOT NULL,
    resultado ENUM('APROBADO','RECHAZADO') NOT NULL,
    documento_respaldo VARCHAR(500) NULL,
    comentario TEXT NULL,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_aprobacion_version (instrumento_version_id),
    CONSTRAINT fk_aprobacion_version
        FOREIGN KEY (instrumento_version_id) REFERENCES instrumento_versiones(id) ON DELETE CASCADE,
    CONSTRAINT fk_aprobacion_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 12. DOCUMENTOS Y AUDITORÍA
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS documentos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    municipalidad_id BIGINT UNSIGNED NOT NULL,
    instrumento_id BIGINT UNSIGNED NULL,
    nombre VARCHAR(500) NOT NULL,
    tipo VARCHAR(100) NULL,
    ruta VARCHAR(1000) NOT NULL,
    hash_archivo VARCHAR(128) NULL,
    tamano BIGINT UNSIGNED NULL,
    usuario_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_documentos_municipalidad (municipalidad_id),
    KEY idx_documentos_instrumento (instrumento_id),
    CONSTRAINT fk_documento_municipalidad
        FOREIGN KEY (municipalidad_id) REFERENCES municipalidades(id) ON DELETE CASCADE,
    CONSTRAINT fk_documento_instrumento
        FOREIGN KEY (instrumento_id) REFERENCES instrumentos(id) ON DELETE SET NULL,
    CONSTRAINT fk_documento_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS auditoria (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    municipalidad_id BIGINT UNSIGNED NULL,
    usuario_id BIGINT UNSIGNED NULL,
    tabla VARCHAR(150) NOT NULL,
    registro_id BIGINT UNSIGNED NULL,
    accion ENUM('CREAR','ACTUALIZAR','ELIMINAR','LOGIN','LOGOUT','EXPORTAR','APROBAR','OBSERVAR') NOT NULL,
    descripcion TEXT NULL,
    ip VARCHAR(45) NULL,
    user_agent VARCHAR(1000) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_auditoria_municipalidad (municipalidad_id),
    KEY idx_auditoria_usuario (usuario_id),
    KEY idx_auditoria_tabla (tabla),
    CONSTRAINT fk_auditoria_municipalidad
        FOREIGN KEY (municipalidad_id) REFERENCES municipalidades(id) ON DELETE SET NULL,
    CONSTRAINT fk_auditoria_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 13. DATOS INICIALES
-- ------------------------------------------------------------
INSERT IGNORE INTO roles (id, nombre, descripcion) VALUES
(1, 'SUPERADMIN', 'Administrador general de CPIGestor'),
(2, 'ADMIN_MUNICIPAL', 'Administrador de una municipalidad'),
(3, 'PLANEAMIENTO', 'Usuario del área de planeamiento'),
(4, 'RECURSOS_HUMANOS', 'Usuario de recursos humanos'),
(5, 'ASESORIA_JURIDICA', 'Usuario de asesoría jurídica'),
(6, 'USUARIO', 'Usuario municipal'),
(7, 'CONSULTA', 'Usuario con acceso de consulta');

INSERT IGNORE INTO permisos (nombre, modulo, descripcion) VALUES
('municipalidades.ver', 'municipalidades', 'Ver municipalidades'),
('municipalidades.gestionar', 'municipalidades', 'Gestionar municipalidades'),
('organizacion.ver', 'organizacion', 'Ver estructura organizacional'),
('organizacion.gestionar', 'organizacion', 'Gestionar estructura organizacional'),
('funciones.ver', 'funciones', 'Ver funciones'),
('funciones.gestionar', 'funciones', 'Gestionar funciones'),
('normativa.ver', 'normativa', 'Ver normativa'),
('normativa.gestionar', 'normativa', 'Gestionar normativa'),
('instrumentos.ver', 'instrumentos', 'Ver instrumentos'),
('instrumentos.gestionar', 'instrumentos', 'Gestionar instrumentos'),
('instrumentos.aprobar', 'instrumentos', 'Aprobar instrumentos'),
('documentos.gestionar', 'documentos', 'Gestionar documentos'),
('reportes.ver', 'reportes', 'Ver reportes'),
('auditoria.ver', 'auditoria', 'Ver auditoría');

-- ------------------------------------------------------------
-- FIN
-- ------------------------------------------------------------
