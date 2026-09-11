## Página 1

Exactamente. La imagen que has subido cambia un punto importante del diseño
que veníamos planteando: en CPIMuni no debemos pensar que una unidad_organica
solamente depende directamente de un órgano. Necesitamos permitir niveles
jerárquicos dentro de las unidades orgánicas, porque aquí aparecen claramente las
Subgerencias.
Por ejemplo, en el organigrama de la MDPH se observa:
### Gerencia Municipal
│
### ├── Gerencia De Administración Y Finanzas
│ │
### │ ├── Subgerencia De Abastecimiento
### │ ├── Subgerencia De Contabilidad
### │ ├── Subgerencia De Tesorería
### │ ├── Subgerencia De Recursos Humanos
### │ └── Subgerencia De Control Patrimonial
│
### ├── Gerencia De Asesoría Legal
│
### ├── Gerencia De Planeamiento Y Presupuesto
│ │
### │ ├── Subgerencia De Presupuesto
### │ ├── Subgerencia De Programación E Inversión
### │ ├── Subgerencia De Racionalización Y Modernización
### │ └── Subgerencia De Formulación De Proyectos
│
### ├── Gerencia De Desarrollo Social Y Servicios Públicos
### │ ├── Subgerencia De Demuna, Omaped Y Ciam
### │ ├── Subgerencia De Educación, Cultura Y Deporte
### │ ├── Subgerencia De Registro Civil
### │ ├── Subgerencia De Transporte
### │ ├── Subgerencia De Seguridad Ciudadana
### │ └── Subgerencia De Riesgos Y Desastres
│
└── ...
1. Entonces, ¿dónde entran las Subgerencias?
En unidades_organicas.
No necesitamos crear una tabla llamada subgerencias.
La diferencia estaría en un campo como:
tipo
Por ejemplo:
Unidad orgánica Tipo
Gerencia de Administración y Finanzas GERENCIA
Subgerencia de Abastecimiento SUBGERENCIA
Subgerencia de Contabilidad SUBGERENCIA
Gerencia de Planeamiento y Presupuesto GERENCIA


---

## Página 2

Unidad orgánica Tipo
Subgerencia de Presupuesto SUBGERENCIA
Y lo más importante:
Necesitamos una relación padre → hijo.
Gerencia de Administración y Finanzas
│
├── Subgerencia de Abastecimiento
├── Subgerencia de Contabilidad
├── Subgerencia de Tesorería
├── Subgerencia de Recursos Humanos
└── Subgerencia de Control Patrimonial
2. Esto es fundamental para CPIMuni
La estructura no debe quedar solamente:
Órgano
↓
Unidad orgánica
↓
Puesto
sino:
### Órgano
│
▼
### Unidad Orgánica
│
### ├── Gerencia
│ │
### │ ├── Subgerencia
### │ │ ├── Puestos
### │ │ └── Funciones
│ │
│ └── ...
│
└── ...
Esto permitirá que CPIMuni reproduzca exactamente estructuras como la de la
imagen.
3. Ejemplo concreto con Planeamiento
Tomemos la parte que aparece en tu organigrama:


---

## Página 3

Nivel 1
### Gerencia Municipal
Nivel 2
### Gerencia De Planeamiento Y Presupuesto
Nivel 3
### Subgerencia De Presupuesto
### Subgerencia De Programación E Inversión
### Subgerencia De Racionalización Y Modernización
### Subgerencia De Formulación De Proyectos
Nivel 4
Después podremos registrar los puestos:
### Subgerencia De Presupuesto
│
├── Subgerente de Presupuesto
├── Especialista en Presupuesto
└── Técnico Administrativo
Y posteriormente las funciones:
### Subgerencia De Presupuesto
│
├── Formular...
├── Coordinar...
├── Supervisar...
└── Evaluar...
4. ¿Cómo debería quedar la base
?
unidades_organicas
Aquí recomiendo que CPIMuni tenga como mínimo:
Campo Ejemplo
id 15
municipalidad_id 1
organo_id 3
parent_id 7
codigo UO-003
nombre Subgerencia de Presupuesto
abreviatura SGP
tipo SUBGERENCIA


---

## Página 4

Campo Ejemplo
nivel 3
descripcion ...
estado ACTIVO
orden 1
La clave nueva que considero muy importante es:
parent_id
que significa:
¿De qué unidad orgánica depende esta unidad?
5. Así quedaría en la base de datos
Por ejemplo:
Registro 1
id: 7
nombre: Gerencia de Planeamiento y Presupuesto
tipo: GERENCIA
parent_id: NULL
Registro 2
id: 8
nombre: Subgerencia de Presupuesto
tipo: SUBGERENCIA
parent_id: 7
Registro 3
id: 9
nombre: Subgerencia de Programación e Inversión
tipo: SUBGERENCIA
parent_id: 7
Registro 4
id: 10
nombre: Subgerencia de Racionalización y Modernización
tipo: SUBGERENCIA
parent_id: 7
Registro 5


---

## Página 5

id: 11
nombre: Subgerencia de Formulación de Proyectos
tipo: SUBGERENCIA
parent_id: 7
Entonces la relación es automática:
7
### Gerencia De Planeamiento Y Presupuesto
│
┌──────────────┼───────────────┐
│ │ │
▼ ▼ ▼
8 9 10
Presupuesto Programación Racionalización
e Inversión y Modernización
│
▼
11
Formulación de
Proyectos
6. ¿Qué ventaja tiene ?
parent_id
Es enorme.
Porque no limitamos CPIMuni a:
Gerencia
↓
Subgerencia
Podremos tener cualquier cantidad de niveles.
Por ejemplo:
Gerencia
↓
Subgerencia
↓
Departamento
↓
Área
↓
Equipo
Si una municipalidad tiene una estructura diferente, CPIMuni también podrá manejarla.


---

## Página 6

7. Y esto también resuelve el
### Organigrama
El organigrama que acabas de subir no debería ser una imagen estática dentro de
CPIMuni.
La idea futura debería ser:
### Base De Datos
↓
Órganos
↓
Unidades orgánicas
↓
Relaciones padre/hijo
↓
Puestos
↓
### Organigrama Automático
Entonces CPIMuni podrá generar algo como:
### Concejo Municipal
│
### Alcaldía
│
### Gerencia Municipal
│
┌─────────────────┼─────────────────┐
│ │ │
▼ ▼ ▼
### Administración Planeamiento Asesoría Legal
### Y Finanzas Y Presupuesto
│ │
│ ┌──────┼──────────┐
│ │ │ │
▼ ▼ ▼ ▼
Abastecimiento Presupuesto PMI Racionalización
Contabilidad Formulación...
Tesorería
### Rr.Hh.
...
Y no tendremos que dibujarlo manualmente cada vez.
8. Pero hay otro detalle importante de tu
imagen
La imagen también muestra elementos como:


---

## Página 7

- Concejo Municipal
- Comisión de Regidores
- Alcaldía
- Órgano de Control Institucional
- Procuraduría Pública Municipal
- Secretaría General
- Gerencia Municipal
- Consejo de Coordinación Local Distrital
- Comité de Administración del Vaso de Leche
- Comité Distrital Ambiental Municipal
- Comité Distrital de Gestión de Riesgos y Desastres
- Junta de Delegados Vecinales
Aquí no debemos asumir automáticamente que todos son "órganos" o "unidades
orgánicas" solo por aparecer en el organigrama.
Para CPIMuni debemos distinguir entre:
### Órganos
### Unidades Orgánicas
### Órganos/Instancias De Coordinación
### Comisiones
### Órganos De Control
### Órganos De Defensa Jurídica
La clasificación definitiva debe salir del ROF que estamos utilizando, no simplemente
de la posición gráfica.
Esto es especialmente importante porque queremos que CPIMuni sea un sistema
técnicamente sólido y no simplemente una reproducción visual del organigrama.
9. Por eso modificaría nuestra estructura
anterior
Inicialmente habíamos planteado:
Órganos
↓
Unidades orgánicas
↓
Puestos
↓
Funciones
Ahora, después de ver este organigrama, recomiendo:
### Municipalidad
│
▼


---

## Página 8

### Estructura Organizacional
│
### ├── Órganos
│
### ├── Unidades Orgánicas
│ │
│ ├── Gerencias
│ │ │
│ │ └── Subgerencias
│ │
│ └── otras unidades
│
### ├── Puestos
│
### ├── Funciones
│
### └── Organigrama
Y la relación interna:
### Órgano
│
▼
### Unidad Orgánica
│
├── parent_id = NULL
│ │
### │ └── Gerencia
│
└── parent_id = ID GERENCIA
│
### └── Subgerencia
│
### └── Puestos
│
### └── Funciones
10. Esto nos lleva a una decisión
importante antes de programar
No deberíamos empezar todavía a crear el CRUD de Organización.
Primero debemos hacer algo mejor:
Tomar el organigrama de la MDPH y convertirlo en una estructura
maestra de CPIMuni.
Por ejemplo:
Nivel Tipo Nombre Depende de
1 Órgano Concejo Municipal —
1 Órgano Alcaldía —


---

## Página 9

Nivel Tipo Nombre Depende de
2 Unidad Gerencia Municipal Alcaldía
Gerencia de Planeamiento y
3 Gerencia Gerencia Municipal
Presupuesto
Gerencia de Planeamiento y
4 Subgerencia Subgerencia de Presupuesto
Presupuesto
Subgerencia de Programación e Gerencia de Planeamiento y
4 Subgerencia
Inversión Presupuesto
Subgerencia de Racionalización y Gerencia de Planeamiento y
4 Subgerencia
Modernización Presupuesto
Subgerencia de Formulación de Gerencia de Planeamiento y
4 Subgerencia
Proyectos Presupuesto
Y hacer lo mismo con toda la estructura que aparece en la imagen.
Después podremos diseñar la base de datos para que esa estructura sea cargable en
CPIMuni y, posteriormente, el sistema pueda generar el organigrama automáticamente.
Ese sería el siguiente paso que recomiendo: convertir este organigrama de la MDPH
2020 en la estructura maestra de Órganos → Unidades → Subgerencias → Puestos
→ Funciones que utilizaremos como modelo para diseñar correctamente las tablas de
CPIMuni.