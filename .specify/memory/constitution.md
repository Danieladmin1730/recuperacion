<!--
Sync Impact Report:
- Version change: 0.0.0 -> 1.0.0
- Modified principles: none (initial constitution)
- Added sections: Core Principles; Security and Data Integrity;
  Development Workflow and Quality Gates; Governance
- Removed sections: none
- - Deferred TODOs: none
-->

# Sistema de Ventas con Asesor IA Constitution

## Core Principles

### I. Arquitectura MVC en PHP
La lógica de negocio de ventas, inventario, caja, usuarios, permisos y reportes
MUST residir en `models/`. Los controladores en `controllers/` MUST organizar el
flujo de solicitudes y coordinar modelos y helpers. La presentación MUST quedar
en `views/`, y las utilidades reutilizables MUST estar en `helpers/`. No se
introducirán frameworks ni patrones incompatibles con la aplicación PHP existente
si no son requeridos por una especificación aprobada.

### II. Convenciones y estructura existentes
Los cambios MUST respetar la estructura de `SistemaVentas-main/`, los nombres
existentes en PascalCase para clases y controladores, los nombres descriptivos
en español para archivos de dominio y el estilo directo de los archivos PHP
actuales. Las consultas de configuración y dependencias existentes en `config/`
y `composer.json` son la fuente vigente de PHP, MySQL y librerías integradas.

### III. Acceso seguro a MySQL
Toda conexión a MySQL o MariaDB MUST usar la configuración existente en
`config/database.php` y la base `sistema_ventas`. Las consultas con datos externos
MUST usar `PDO::prepare()` y parámetros enlazados; no se construirán sentencias
SQL por concatenación de valores no confiables. Las operaciones financieras o
de inventario MUST usar transacciones cuando múltiples registros compartan un
mismo cambio de estado.

### IV. Validación de entrada y escape de salida
Cada dato recibido por `$_GET`, `$_POST`, archivos o sesiones MUST validarse en
el servidor antes de usarlo. Los controladores y vistas MUST validar formatos,
rangos y estados permitidos según la operación. Toda salida dinámica en PHP
MUST escaparse mediante el helper `e()` o `htmlspecialchars(..., ENT_QUOTES,
'UTF-8')`, evitando que datos no confiables se interpreten como HTML o JavaScript.

### V. Pruebas para toda funcionalidad nueva
Toda funcionalidad nueva MUST incluir pruebas que verifiquen su comportamiento.
Las pruebas MUST cubrir al menos la ruta principal y los casos de fallo
relevantes, incluyendo validación, permisos, cálculos financieros, movimientos
de inventario y compatibilidad de datos cuando correspondan. Mientras no exista
una suite automatizada, el cambio MUST incluir una validación reproducible y
documentar su resultado; no se acepta una implementación sin evidencia.

## Seguridad y Datos

Las sesiones, permisos y autenticación MUST verificarse antes de ejecutar
acciones sensibles. Las consultas MUST protegerse contra inyección SQL mediante
consultas preparadas. Los formularios MUST preservar la protección CSRF existente
cuando se modifiquen o agreguen operaciones de escritura. Los secretos locales
MUST permanecer fuera del control de versiones.

No se modificará el esquema de base de datos sin un script SQL documentado,
versionado junto al cambio y probado con los datos existentes. Los cambios en
`database.sql` o scripts de actualización MUST indicar claramente tablas,
columnas, restricciones o datos afectados.

## Desarrollo y Compatibilidad

Los cambios MUST ser pequeños, revisables y compatibles con las funcionalidades
existentes. Antes de implementar se MUST identificar el comportamiento actual
en los modelos, controladores, vistas y helpers relacionados. No se eliminarán
flujos ni rutas existentes sin una migración explícita aprobada por la
especificación.

El código y los comentarios nuevos o modificados MUST escribirse en español,
seguir el estilo actual y evitar cambios innecesarios de formato. Las variables
y funciones SHOULD conservar nombres claros y consistentes con el dominio. Cada
implementación MUST actualizar directamente la documentación relacionada solo
cuando el comportamiento o la configuración cambien.

## Governance

Esta constitución gobierna las decisiones de diseño y entrega del proyecto.
Las enmiendas requieren una razón documentada, una actualización semántica de
versión y la revisión de las prácticas afectadas. Los contribuyentes MUST
aplicar esta constitución durante la especificación, planificación,
implementación y revisión.

El versionado sigue el formato SemVer:

- MAJOR para cambios incompatibles con la gobernanza o eliminación/redefinición
  de principios.
- MINOR para agregar principios o ampliar materialmente una sección.
- PATCH para aclaraciones, correcciones o cambios no semánticos.

El cumplimiento MUST revisarse al finalizar cada funcionalidad y antes de una
entrega. La revisión SHALL confirmar arquitectura, seguridad, integridad de
datos, compatibilidad, pruebas y documentación.

**Version**: 1.0.0 | **Ratified**: 2026-11-24 | **Last Amended**: 2026-11-24