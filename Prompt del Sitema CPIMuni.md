# Documento convertido por PDF2MD AI

> **Páginas:** 34  
> **OCR utilizado:** 0 páginas  
> **Palabras totales:** 3635

---

## Página 1

A continuación te dejo un resumen maestro ordenado y, al final, un PROMPT
MAESTRO listo para utilizar para continuar el desarrollo con IA.
### 1. Nombre Del Sistema
CPIMuni
Plataforma de Gestión Municipal Inteligente
Propósito general:
Sistema web multimunicipal diseñado para gestionar, automatizar, relacionar, versionar,
revisar, aprobar y monitorear los principales instrumentos de gestión y procesos de
planificación y administración municipal.
La idea no es construir simplemente un sistema de archivos PDF o CRUDs.
La visión es:
CPIMuni
│
┌────────────┴────────────┐
│ │
### Estructura Gestión
### Organizacional Municipal
│ │
└────────────┬────────────┘
│
### Instrumentos
### De Gestión
│
┌───────────┼───────────┐
│ │ │
### Pei/Poi Presupuesto Pmi
│ │ │
└───────────┼───────────┘
│
CMN
│
### Contrataciones
│
### Ejecución
│
### Seguimiento
│
### Evaluación
### 2. Objetivo General


---

## Página 2

El objetivo de CPIMuni es centralizar y automatizar la gestión institucional
municipal, permitiendo que una municipalidad pueda construir y administrar su
estructura organizacional e instrumentos de gestión desde una única plataforma.
El sistema debe permitir:
- administrar municipalidades;
- administrar estructuras organizacionales;
- administrar órganos y unidades orgánicas;
- administrar puestos y funciones;
- gestionar ROF y otros instrumentos;
- gestionar PEI;
- gestionar OEI y AEI;
- gestionar POI;
- gestionar actividades operativas e inversiones;
- relacionar responsables institucionales;
- integrar programación presupuestal;
- integrar CMN;
- integrar PMI;
- gestionar documentos;
- controlar versiones;
- realizar revisiones;
- registrar observaciones;
- controlar aprobaciones;
- mantener trazabilidad;
- generar reportes;
- generar indicadores;
- proporcionar un dashboard de gestión;
- permitir trabajar con múltiples municipalidades.
### 3. Principio Central Del
### Sistema
La estructura organizacional es la columna vertebral de CPIMuni.
No debemos diseñar los módulos como sistemas independientes.
Por ejemplo:
Municipalidad
↓
Órgano
↓
Unidad Orgánica
↓
Puesto
↓
Función


---

## Página 3

↓
Responsabilidad
↓
PEI
↓
OEI
↓
AEI
↓
POI
↓
Actividad Operativa
↓
CMN / Presupuesto / PMI
↓
Contratación
↓
Ejecución
↓
Seguimiento
De esta manera podemos responder preguntas como:
¿Qué unidad es responsable de una actividad del POI?
¿Qué función sustenta esa responsabilidad?
¿Qué OEI/AEI está atendiendo?
¿Qué recursos requiere?
¿Está presupuestada?
¿Está incluida en el CMN?
¿Cuál es su avance?
Ese es uno de los elementos que diferencia a CPIMuni de un simple gestor documental.
### 4. Arquitectura
### Multimunicipal
CPIMuni será multi-tenant por municipalidad.
Ejemplo:
CPIMuni
│
├── Municipalidad A
│ ├── Usuarios
│ ├── Organización


---

## Página 4

│ ├── Instrumentos
### │ ├── Pei
### │ ├── Poi
│ └── Presupuesto
│
├── Municipalidad B
│ ├── Usuarios
│ ├── Organización
│ ├── Instrumentos
### │ ├── Pei
### │ ├── Poi
│ └── Presupuesto
│
└── Municipalidad C
├── Usuarios
├── Organización
├── Instrumentos
### ├── Pei
### ├── Poi
└── Presupuesto
Regla fundamental
Todo dato específico de una municipalidad debe estar relacionado con:
municipalidad_id
excepto los catálogos verdaderamente globales.
### 5. Superadmin
El SUPERADMIN es el administrador global de CPIMuni.
Puede:
- administrar municipalidades;
- crear municipalidades;
- editar municipalidades;
- activar/desactivar municipalidades;
- administrar usuarios globalmente;
- administrar roles;
- administrar permisos;
- administrar catálogos maestros;
- duplicar una municipalidad desde una estructura existente;
- controlar configuraciones globales.
Muy importante
El SUPERADMIN puede ver múltiples municipalidades.
Pero un usuario municipal debe trabajar solamente dentro de su municipalidad.


---

## Página 5

### 6. Duplicación De
### Municipalidades
Esta es una característica importante que definimos.
El SUPERADMIN debe poder:
Crear una nueva municipalidad utilizando otra municipalidad como plantilla.
Ejemplo:
Municipalidad origen
│
### │ Duplicar Estructura
▼
Nueva municipalidad
Se puede copiar
- órganos;
- unidades orgánicas;
- puestos;
- funciones;
- estructura jerárquica;
- organigrama;
- determinadas plantillas de instrumentos.
NO se debe copiar
- usuarios;
- contraseñas;
- auditoría;
- documentos históricos;
- revisiones;
- observaciones;
- aprobaciones;
- historial de acceso.
Además, al duplicar:
ID origen → ID nuevo
deben generarse nuevos identificadores.
Y las relaciones padre/hijo deben reconstruirse con los nuevos IDs.


---

## Página 6

### 7. Catálogos Maestros
Uno de los elementos que acabamos de definir es:
catalogo_estructura_organizacional
Esta tabla será un catálogo maestro global.
Característica fundamental
No debe depender de:
municipalidad_id
porque será utilizado por todas las municipalidades.
Ejemplo
Puede contener:
Naturaleza
- Gobierno
- Alta Dirección
- Control
- Defensa Jurídica
- Asesoramiento
- Apoyo
- Línea
- Coordinación
- Consultivo
Tipo
- Concejo
- Alcaldía
- Gerencia Municipal
- Gerencia
- Subgerencia
- Oficina
- Unidad
- Área
- Departamento
- Secretaría
- Comité
- Comisión
- Consejo
- Junta
- Equipo de Trabajo


---

## Página 7

La clasificación definitiva de una municipalidad debe corresponder a su ROF aprobado;
estas categorías constituyen el modelo técnico del sistema.
### 8. Crud Del Catálogo Maestro
El CRUD de:
catalogo_estructura_organizacional
debe ser:
Visible únicamente para:
### Superadmin
Y además debe estar protegido en backend.
No basta con ocultar el menú.
Si otro usuario intenta acceder directamente a la URL:
/catalogos/estructura-organizacional
debe recibir:
403 Forbidden
Operaciones
- Crear
- Consultar
- Editar
- Activar
- Desactivar
Para este catálogo se recomienda no eliminar físicamente elementos que puedan estar
relacionados con estructuras existentes.
### 9. Estructura Organizacional
El sistema ya tiene las tablas:
organos
unidades_organicas
puestos


---

## Página 8

funciones
La estructura debe ser jerárquica y flexible.
No debemos programar:
nivel 1
nivel 2
nivel 3
nivel 4
de forma rígida.
Debe utilizarse:
padre_id
para permitir diferentes estructuras municipales.
Por ejemplo:
Gerencia Municipal
│
├── Gerencia de Planeamiento
│ ├── Subgerencia de Presupuesto
│ └── Subgerencia de Programación e Inversión
│
└── Gerencia de Desarrollo Económico
├── Subgerencia de Turismo
└── Subgerencia de Comercialización
### 10. Organización Propuesta
El módulo Organización tendrá:
Organización
│
├── Estructura organizacional
├── Órganos
├── Unidades orgánicas
├── Puestos
├── Funciones
└── Organigrama
La pantalla de estructura deberá permitir visualizar la organización como:
- árbol;
- escalera;
- estructura jerárquica;
- tabla;
- búsqueda;
- filtros.


---

## Página 9

### 11. Puestos
Los puestos estarán relacionados con:
Municipalidad
↓
Unidad Orgánica
↓
Puesto
Campos importantes:
- código;
- denominación;
- nivel;
- finalidad;
- requisitos;
- competencias;
- estado.
### 12. Funciones
Las funciones estarán relacionadas con:
Municipalidad
↓
Unidad Orgánica
↓
Puesto
↓
Función
La tabla contempla elementos como:
- código;
- descripción;
- tipo;
- fuente;
- estado;
- fecha de inicio;
- fecha de fin.
Esto será fundamental posteriormente para relacionar funciones con los instrumentos
institucionales.


---

## Página 10

### 13. Instrumentos De Gestión
CPIMuni debe contemplar un módulo general:
Instrumentos de Gestión
│
### ├── Rof
### ├── Mpp / Mof
### ├── Cap / Cpe
### ├── Tupa
├── Directivas
├── Reglamentos
└── Otros instrumentos
Pero estos no deben ser solamente documentos cargados.
El sistema debe permitir:
- crear;
- versionar;
- estructurar;
- editar;
- revisar;
- observar;
- aprobar;
- publicar;
- mantener historial.
### 14. Sistema De Versionamiento
Un instrumento puede tener:
ROF
│
├── Versión 1
├── Versión 2
├── Versión 3
└── Versión vigente
Cada versión debe conservar:
- fecha;
- usuario;
- estado;
- motivo;
- documento;
- observaciones;
- aprobación.


---

## Página 11

Esto evita perder el historial.
### 15. Revisión Y Aprobación
El sistema contempla:
Borrador
↓
En revisión
↓
Observado
↓
Subsanado
↓
Aprobado
↓
Vigente
Y debe existir trazabilidad.
Tablas existentes:
revisiones
observaciones
aprobaciones
### 16. Documentos
El módulo documental permitirá relacionar archivos con:
- instrumentos;
- versiones;
- organización;
- normativa;
- procesos;
- usuarios.
La documentación debe mantener:
- nombre;
- tipo;
- versión;
- fecha;
- usuario;
- relación;
- estado.


---

## Página 12

### 17. Normativa
La normativa debe poder relacionarse con los instrumentos.
La estructura existente contempla:
normas
funcion_norma
Esto permitirá establecer relaciones como:
Norma
↓
Función
↓
Unidad Orgánica
↓
Instrumento
### 18. Planeamiento
### Institucional
Una segunda gran capa será:
Planeamiento
│
### ├── Pei
### ├── Oei
### ├── Aei
├── Indicadores
### ├── Poi
└── Actividades Operativas
La arquitectura debe permitir relacionar:
PEI
↓
OEI
↓
AEI
↓
POI
↓
Actividad Operativa
### 19. Relación Con La
### Organización


---

## Página 13

Aquí está uno de los mayores valores de CPIMuni.
Una actividad del POI debe poder relacionarse con:
Actividad Operativa
↓
Unidad responsable
↓
Puesto / responsable
↓
Función
↓
AEI
↓
OEI
Así se evita que el planeamiento esté separado de la organización real.
### 20. Presupuesto
La arquitectura futura contempla:
Presupuesto
│
### ├── Pia
### ├── Pim
├── Modificaciones
├── Programación
└── Ejecución
La información presupuestal debe poder relacionarse con las actividades institucionales.
### 21. Cmn
El Cuadro Multianual de Necesidades debe integrarse con:
POI
↓
Actividad Operativa
↓
Necesidad
↓
CMN
↓
Presupuesto
↓
Contratación


---

## Página 14

Esto permitirá identificar necesidades de bienes, servicios y otros recursos asociados a
la planificación.
### 22. Pmi E Inversiones
Otra capa:
PMI
│
├── Cartera
├── Inversiones
### ├── Cui
├── Programación
└── Seguimiento
Las inversiones podrán relacionarse con:
PEI
↓
### Oei / Aei
↓
POI
↓
Inversión
↓
PMI
### 23. Abastecimiento Y
### Contrataciones
La arquitectura futura contempla:
Abastecimiento
│
### ├── Cmn
├── Requerimientos
├── Contrataciones
├── Órdenes
├── Contratos
└── Seguimiento
Esto permitirá avanzar desde la necesidad institucional hasta su atención.
### 24. Ejecución


---

## Página 15

La etapa posterior deberá permitir integrar información de:
Programación
↓
Presupuesto
↓
Contratación
↓
Ejecución
↓
Seguimiento
Cuando existan fuentes de datos disponibles, CPIMuni podrá incorporar ejecución física
y financiera.
### 25. Seguimiento Y Evaluación
El sistema debe contar con:
Seguimiento
│
├── Indicadores
├── Metas
├── Avance físico
├── Avance financiero
├── Alertas
└── Evaluación
Ejemplo:
Actividad
│
├── Meta programada
├── Meta ejecutada
├── % avance
├── Presupuesto
└── Ejecución
### 26. Dashboard
El Dashboard no debe limitarse a:
50 usuarios
20 órganos
100 documentos
Debe ser un Dashboard institucional.
Puede mostrar:


---

## Página 16

- PEI vigente;
### • Oei;
### • Aei;
### • Poi;
- actividades;
- presupuesto;
- ejecución;
### • Cmn;
- inversiones;
- instrumentos;
- documentos pendientes;
- observaciones;
- aprobaciones;
- alertas;
- avance de metas.
### 27. Roles
Los roles inicialmente definidos son:
### Superadmin
### Admin_Municipal
### Planeamiento
### Recursos_Humanos
### Asesoria_Juridica
### Usuario
### Consulta
La diferencia entre roles debe gestionarse mediante permisos.
### 28. Permisos
Se han definido permisos como:
municipalidades.ver
municipalidades.gestionar
organizacion.ver
organizacion.gestionar
funciones.ver
funciones.gestionar
normativa.ver
normativa.gestionar
instrumentos.ver
instrumentos.gestionar


---

## Página 17

instrumentos.aprobar
documentos.gestionar
reportes.ver
auditoria.ver
En el futuro se pueden ampliar por módulo.
### 29. Seguridad Multimunicipal
Una regla crítica:
Un usuario de una municipalidad nunca debe poder acceder a información de otra
municipalidad manipulando un ID en la URL.
Por ejemplo:
/organos/25
no debe ser suficiente para acceder al órgano 25 si pertenece a otra municipalidad.
La autorización debe verificarse en servidor.
### 30. Auditoría
La tabla:
auditoria
debe registrar acciones importantes.
Ejemplo:
Usuario
↓
Acción
↓
Módulo
↓
Registro
↓
Fecha/hora
↓
Resultado
Esto permitirá saber quién:


---

## Página 18

- creó;
- modificó;
- activó;
- desactivó;
- aprobó;
- observó;
- cambió una versión.
### 31. Tecnología
La tecnología definida para CPIMuni es:
Backend
Laravel 12.69.2
Lenguaje
### Php 8.2.12
Base de datos
MariaDB 10.4.32 / MySQL compatible
Servidor local
### Xampp
Frontend
- Blade
### • Html5
### • Css3
- JavaScript
- componentes reutilizables
- iconos mediante la biblioteca actualmente utilizada en el proyecto
Entorno
C:\xampp\htdocs\cpimuni
Base de datos
cpimuni


---

## Página 19

### 32. Regla Muy Importante
### Sobre La Base De Datos
CPIMuni ya tiene una base de datos propia con tablas construidas.
Por tanto:
No ejecutar php artisan migrate indiscriminadamente sobre cpimuni.
Primero se debe comprobar el esquema existente y crear migraciones solamente cuando
se haya decidido formalmente sincronizar la estructura.
Esto evita destruir o modificar accidentalmente las tablas existentes.
### 33. Modelos Principales Ya
### Definidos
Ya existe la base de modelos para:
Municipalidad
Usuario
Rol
Permiso
Y las relaciones principales:
Usuario
├── Municipalidad
└── Rol
Rol
├── Usuarios
└── Permisos
Permiso
└── Roles
La autenticación ya está funcionando.
### 34. Autenticación
El login utiliza la tabla:
usuarios


---

## Página 20

El usuario tiene:
municipalidad_id
rol_id
La autenticación ya fue configurada para el modelo:
App\Models\Usuario
Y actualmente el login funciona.
### 35. Diseño Visual / Theme
También hemos identificado que debemos definir formalmente el Theme de
CPIMuni antes de seguir creciendo en módulos.
La propuesta visual es institucional y moderna.
Color principal
### #0B3C6D
Complementarios
### #082F55
### #2563Eb
### #F5F7Fa
### #Ffffff
### #1E293B
### #64748B
Estados:
Éxito #16A34A
Advertencia #D97706
Peligro #DC2626
### 36. Iconos
Se detectó un problema real en la interfaz:
El icono de retroceso/paginación está apareciendo gigantesco.
Esto indica que debemos hacer una auditoría del CSS antes de continuar.
No debemos aplicar:


---

## Página 21

svg {
width: 100%;
height: 100%;
}
de manera global.
Debemos utilizar clases específicas y una escala uniforme.
Propuesta:
Icono pequeño 14 px
Icono estándar 16 px
Sidebar 18 px
Indicadores 22 px
Iconos destacados 28 px
### 37. Diseño Del Sidebar
El menú general proyectado es:
CPIMuni
### Principal
└── Dashboard
### Municipalidad
└── Mi Municipalidad
### Organización
├── Estructura organizacional
├── Órganos
├── Unidades orgánicas
├── Puestos
├── Funciones
└── Organigrama
### Planeamiento
### ├── Pei
### ├── Oei
### ├── Aei
### ├── Poi
├── Actividades Operativas
└── Indicadores
### Presupuesto
├── Programación
### ├── Pia
### ├── Pim
├── Modificaciones
└── Ejecución
### Abastecimiento
### ├── Cmn
├── Requerimientos
├── Contrataciones


---

## Página 22

└── Seguimiento
### Inversiones
### ├── Pmi
├── Cartera
├── Inversiones
└── Seguimiento
### Instrumentos
### ├── Rof
### ├── Mpp/Mof
### ├── Cap/Cpe
### ├── Tupa
├── Directivas
└── Otros
### Normativa
### Documentos
### Reportes
### Seguimiento Y Evaluación
### Administración
├── Municipalidades
├── Usuarios
├── Roles
├── Permisos
└── Catálogos Maestros
└── Estructura Organizacional
### 38. Crud
Antes de seguir agregando módulos, debemos hacer una auditoría de los CRUD
existentes.
Para cada CRUD:
### Listar
↓
VER
↓
### Crear
↓
### Editar
↓
### Activar/Desactivar
↓
### Validar Permisos
↓
### Validar Municipalidad
Y comprobar:
- validaciones;


---

## Página 23

- mensajes;
- relaciones;
- filtros;
- paginación;
- permisos;
- seguridad;
- diseño;
- responsive.
### 39. Eliminación
Para información institucional importante se recomienda:
### Activo
### Inactivo
antes que eliminación física.
Especialmente en:
- órganos;
- unidades;
- puestos;
- funciones;
- instrumentos;
- versiones;
- normativa.
La razón es mantener trazabilidad histórica.
### 40. Orden De Desarrollo
### Recomendado
Yo establecería ahora este orden definitivo:
FASE 1 — Base visual
1. Auditoría CSS.
2. Definir Theme.
3. Corregir iconos.
4. Normalizar botones.
5. Normalizar tablas.
6. Normalizar formularios.


---

## Página 24

7. Normalizar mensajes.
8. Normalizar sidebar/navbar.
FASE 2 — Seguridad
9. Roles.
10. Permisos.
11. Middleware.
12. aislamiento por municipalidad.
### 13. Superadmin.
FASE 3 — Catálogos maestros
14. catalogo_estructura_organizacional.
15. CRUD exclusivo SUPERADMIN.
16. Activar/desactivar.
17. Catálogos complementarios.
FASE 4 — Organización
18. Órganos.
19. Unidades orgánicas.
20. Puestos.
21. Funciones.
22. Estructura jerárquica.
23. Organigrama.
FASE 5 — Multimunicipalidad
24. Crear municipalidad.
25. Duplicar estructura.
26. Mapeo de IDs.
27. Plantillas.
FASE 6 — Instrumentos
### 28. Rof.
### 29. Mpp/Mof.
### 30. Cap/Cpe.
### 31. Tupa.
32. Directivas.
33. Versionamiento.
34. Revisión.
35. Observaciones.
36. Aprobaciones.
FASE 7 — Planeamiento
### 37. Pei.


---

## Página 25

### 38. Oei.
### 39. Aei.
40. Indicadores.
### 41. Poi.
42. Actividades operativas.
FASE 8 — Integración
### 43. Pmi.
### 44. Cmn.
### 45. Pia/Pim.
46. Contrataciones.
47. Ejecución.
FASE 9 — Inteligencia
48. Dashboard.
49. Indicadores.
50. Alertas.
51. Reportes.
52. Seguimiento.
53. Evaluación.
54. Trazabilidad.
### 41. Prompt Maestro Para
### Construir Cpimuni
Este sería el prompt base que recomiendo utilizar:
### Actúa Como Arquitecto De Software Senior, Desarrollador
### Full Stack Laravel, Especialista En Sistemas De Gestión
### Municipal, Diseño De Bases De Datos Multi-Tenant, Ux/Ui
### Institucional Y Automatización De Instrumentos De Gestión
### Pública.
### Proyecto:
CPIMuni – Plataforma de Gestión Municipal Inteligente.
### Objetivo:
Desarrollar una plataforma web multimunicipal para administrar,
automatizar, relacionar, versionar, revisar, aprobar y monitorear
los instrumentos de gestión municipal, integrándolos con la
estructura organizacional, planeamiento institucional, presupuesto,
abastecimiento, inversiones, ejecución y seguimiento.
### Tecnología Obligatoria:
- Laravel 12.69.2
### - Php 8.2.12
- MariaDB 10.4.32
- MySQL compatible


---

## Página 26

- Blade
### - Html5
### - Css3
- JavaScript
- XAMPP para desarrollo local
### Base De Datos:
La base de datos existente se denomina `cpimuni`.
NO ejecutar php artisan migrate indiscriminadamente.
Antes de modificar la estructura de base de datos se debe revisar
el esquema existente y preservar las tablas y relaciones actuales.
### Arquitectura:
CPIMuni debe ser multi-municipalidad.
Toda información propia de una municipalidad debe relacionarse
mediante `municipalidad_id`.
Los usuarios municipales solamente pueden acceder a información
de su propia municipalidad.
SUPERADMIN tiene alcance global y puede administrar todas las
municipalidades.
### Estructura Central:
Municipalidad
→ Órganos
→ Unidades Orgánicas
→ Puestos
→ Funciones
### → Pei
### → Oei
### → Aei
### → Poi
→ Actividades Operativas/Inversiones
→ CMN/Presupuesto/PMI
→ Contrataciones
→ Ejecución
→ Seguimiento
→ Evaluación.
### Catálogo Maestro:
Utilizar `catalogo_estructura_organizacional` como catálogo
maestro global.
Esta tabla NO debe depender de municipalidad_id.
Debe tener CRUD exclusivo para SUPERADMIN.
El menú del catálogo solamente debe ser visible para SUPERADMIN,
pero además todas las rutas y acciones deben estar protegidas
mediante autorización en backend.
Operaciones:
- listar
- consultar
- crear
- editar
- activar
- desactivar


---

## Página 27

Preferir desactivación lógica sobre eliminación física cuando
existan relaciones con información institucional.
### Organización:
Implementar estructura jerárquica flexible.
No hardcodear niveles rígidos.
Utilizar relaciones padre/hijo como:
- organo_padre_id
- unidad_padre_id
La estructura debe soportar diferentes ROF y diferentes
organizaciones municipales.
### Multimunicipalidad:
SUPERADMIN debe poder crear una nueva municipalidad desde una
municipalidad existente como plantilla.
Duplicar:
- órganos
- unidades orgánicas
- puestos
- funciones
- estructura jerárquica
- organigrama
- plantillas seleccionadas
NO duplicar:
- usuarios
- contraseñas
- auditoría
- documentos históricos
- revisiones
- observaciones
- aprobaciones
- historial de acceso.
Durante la duplicación deben crearse nuevos IDs y reconstruirse
correctamente las relaciones entre registros.
### Roles:
### - Superadmin
### - Admin_Municipal
### - Planeamiento
### - Recursos_Humanos
### - Asesoria_Juridica
### - Usuario
### - Consulta
### Permisos:
Utilizar un sistema de permisos granular.
Ejemplos:
- municipalidades.ver
- municipalidades.gestionar
- organizacion.ver
- organizacion.gestionar
- funciones.ver
- funciones.gestionar


---

## Página 28

- normativa.ver
- normativa.gestionar
- instrumentos.ver
- instrumentos.gestionar
- instrumentos.aprobar
- documentos.gestionar
- reportes.ver
- auditoria.ver
### Seguridad:
Nunca confiar únicamente en ocultar botones o menús.
Todas las operaciones deben validar:
1. autenticación;
2. rol;
3. permiso;
4. municipalidad;
5. existencia del registro;
6. estado del registro.
Un usuario de una municipalidad no puede acceder a registros de
otra municipalidad manipulando IDs en la URL.
### Instrumentos De Gestión:
Crear arquitectura para:
### - Rof
### - Mpp/Mof
### - Cap/Cpe
### - Tupa
- Directivas
- Reglamentos
- Otros instrumentos.
Los instrumentos deben soportar:
- estructura
- documentos
- versiones
- revisión
- observaciones
- subsanación
- aprobación
- vigencia
- historial.
### Estados Propuestos:
### - Borrador
### - En_Revision
### - Observado
### - Subsanado
### - Aprobado
### - Vigente
### - No_Vigente
### Planeamiento:
Diseñar integración:
PEI
### → Oei
### → Aei
→ Indicadores
### → Poi
→ Actividades Operativas/Inversiones.


---

## Página 29

Las actividades deben poder relacionarse con:
- municipalidad;
- unidad orgánica responsable;
- responsable;
- función;
### - Oei;
### - Aei;
- indicador;
- meta;
- presupuesto;
### - Cmn;
- inversión.
### Presupuesto:
Preparar arquitectura para:
### - Pia
### - Pim
- modificaciones
- programación
- ejecución.
### Abastecimiento:
Preparar arquitectura para:
### - Cmn
- necesidades
- requerimientos
- contrataciones
- órdenes
- contratos
- seguimiento.
### Inversiones:
Preparar arquitectura para:
### - Pmi
- cartera
- inversiones
### - Cui
- programación
- seguimiento.
### Documentos:
Permitir relacionar documentos con:
- municipalidad;
- instrumento;
- versión;
- norma;
- organización;
- proceso.
### Auditoría:
Registrar:
- usuario;
- municipalidad;
- acción;
- módulo;
- registro afectado;
- fecha/hora;
- información relevante de la operación.
### Dashboard:


---

## Página 30

Diseñar un dashboard institucional, no únicamente administrativo.
Mostrar:
### - Pei;
### - Poi;
- actividades;
- indicadores;
- metas;
- presupuesto;
- ejecución;
### - Cmn;
- inversiones;
- instrumentos;
- documentos pendientes;
- revisiones;
- aprobaciones;
- alertas.
### Diseño Ui/Ux:
Crear un Theme institucional moderno y profesional.
Color principal:
### #0B3C6D
Colores complementarios:
### #082F55
### #2563Eb
### #F5F7Fa
### #Ffffff
### #1E293B
### #64748B
Estados:
### #16A34A
### #D97706
### #Dc2626
Utilizar variables CSS globales para colores, tipografías,
espaciados, bordes, sombras y tamaños.
### Iconos:
Utilizar una escala consistente:
XS = 14px
SM = 16px
MD = 18px
LG = 22px
XL = 28px
No aplicar reglas globales que hagan que todos los SVG tengan
width/height 100%.
Los iconos del sidebar deben ser aproximadamente 18px.
Los iconos de botones aproximadamente 16px.
Los iconos de tarjetas aproximadamente 22px.
### Sidebar:
Diseñar menú modular:
Dashboard


---

## Página 31

Municipalidad
Organización
- Estructura organizacional
- Órganos
- Unidades orgánicas
- Puestos
- Funciones
- Organigrama
Planeamiento
### - Pei
### - Oei
### - Aei
### - Poi
- Actividades Operativas
- Indicadores
Presupuesto
- Programación
### - Pia
### - Pim
- Modificaciones
- Ejecución
Abastecimiento
### - Cmn
- Requerimientos
- Contrataciones
- Seguimiento
Inversiones
### - Pmi
- Cartera
- Inversiones
- Seguimiento
Instrumentos
### - Rof
### - Mpp/Mof
### - Cap/Cpe
### - Tupa
- Directivas
- Otros
Normativa
Documentos
Reportes
Seguimiento y Evaluación
Administración
- Municipalidades
- Usuarios
- Roles
- Permisos
- Catálogos Maestros
### Crud:
Todos los CRUD deben implementar:
- listar;
- buscar;


---

## Página 32

- filtrar;
- paginar;
- crear;
- editar;
- consultar;
- activar/desactivar;
- validar permisos;
- validar municipalidad;
- mensajes de éxito/error;
- confirmación de operaciones críticas.
Antes de construir nuevos módulos, revisar los CRUD existentes
y comprobar que Crear, Ver, Editar y Activar/Desactivar funcionan
correctamente.
No eliminar físicamente información histórica cuando pueda
romper relaciones o trazabilidad.
### Metodología De Desarrollo:
No generar grandes cantidades de código sin comprobar la
estructura existente.
Antes de modificar:
1. revisar archivos existentes;
2. revisar rutas;
3. revisar modelos;
4. revisar controladores;
5. revisar vistas;
6. revisar tablas;
7. identificar dependencias;
8. modificar solamente lo necesario.
Después de cada módulo:
1. probar rutas;
2. probar CRUD;
3. probar validaciones;
4. probar permisos;
5. probar aislamiento municipal;
6. comprobar errores;
7. comprobar interfaz;
8. comprobar responsive.
### Importante:
No crear tablas duplicadas si ya existe una tabla equivalente.
No modificar la base de datos existente sin verificar primero
su estructura.
No reemplazar código funcional sin identificar la dependencia.
El desarrollo debe ser incremental, modular, seguro, mantenible
y preparado para múltiples municipalidades.
### Objetivo Final:
Construir una plataforma integral donde la estructura
organizacional sea la columna vertebral y los instrumentos de
gestión estén relacionados entre sí, permitiendo trazabilidad
desde la función institucional hasta el planeamiento, presupuesto,
abastecimiento, inversión, ejecución, seguimiento y evaluación.


---

## Página 33

### 42. La Visión Final De Cpimuni
La idea que hemos ido construyendo puede resumirse así:
### Cpimuni
│
### Multimunicipalidad
│
┌──────────────┴──────────────┐
│ │
### Catálogos Maestros Municipalidad
│ │
### │ Estructura Organizacional
│ │
│ ┌──────────────┼─────────────┐
│ │ │ │
### │ Órganos Unidades Puestos
│ │ │ │
│ └──────────────┼─────────────┘
│ │
### │ Funciones
│ │
└─────────────────────────────┤
│
### Instrumentos
│
┌──────────────────────┼──────────────────────┐
│ │ │
### Rof Pei Poi
│ │ │
### │ Oei / Aei Actividades
│ │ │
└──────────────────────┼──────────────────────┘
│
┌────────────┼────────────┐
│ │ │
### Pmi Cmn Presupuesto
│ │ │
└────────────┼────────────┘
│
### Contratación
│
### Ejecución
│
### Seguimiento
│
### Evaluación
│
### Reportes
│
### Dashboard
Esta es la arquitectura que considero que debemos conservar como norte del
proyecto.
Y, dado el estado actual de CPIMuni, el siguiente paso concreto debería ser hacer
una auditoría del proyecto existente (CSS + Layout + CRUD actuales +
catalogo_estructura_organizacional) antes de programar nuevas


---

## Página 34

funcionalidades. Así convertimos todo lo que ya hemos construido en una base estable
y no acumulamos correcciones posteriores.