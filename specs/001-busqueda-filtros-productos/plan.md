# Implementation Plan: Búsqueda y filtros de productos

**Branch**: `001-busqueda-filtros-productos` | **Date**: 2026-11-24 | **Spec**: [spec.md](./spec.md)

**Input**: Feature specification from `specs/001-busqueda-filtros-productos/spec.md`

## Summary

Extender el listado existente de productos para filtrar en el servidor por texto, categoría y disponibilidad de stock, conservando los filtros actuales de activo/inactivo y stock bajo, así como el listado predeterminado. La consulta se mantiene en `Producto::listar()` mediante PDO y parámetros preparados; la vista existente presenta los controles y resultados con escape mediante `e()`. Se añadirá PHPUnit como dependencia de desarrollo de Composer y pruebas aisladas del modelo usando PDO SQLite en memoria.

## Technical Context

**Language/Version**: PHP `^8.1` (requisito actual del proyecto; PHP 8.2.12 disponible en el entorno inspeccionado).

**Primary Dependencies**: Composer; dependencias actuales de producción sin cambios; PHPUnit `^10.5` como dependencia de desarrollo, compatible con PHP 8.1.

**Storage**: MySQL/MariaDB por PDO a través de `SistemaVentas-main/config/database.php`; sin cambios de esquema. SQLite en memoria se usa únicamente para las pruebas automatizadas del modelo.

**Testing**: PHPUnit 10.5, configurado para descubrir pruebas en `SistemaVentas-main/tests/`; ejecutar con `vendor/bin/phpunit` (en Windows, `vendor\bin\phpunit.bat`). Las pruebas del modelo requieren `pdo_sqlite`.

**Target Platform**: Aplicación web PHP existente, ejecutada en el servidor configurado para el sistema; compatible con PHP 8.1 o superior.

**Project Type**: Aplicación web monolítica PHP con arquitectura MVC.

**Performance Goals**: No se introduce una meta de latencia nueva. Se conserva el límite actual de 500 productos y el orden del listado sin criterios nuevos.

**Constraints**: Mantener MVC, el controlador y la vista actuales, la conexión PDO existente, consultas preparadas, validación de parámetros, escape con `e()`, compatibilidad del listado y filtros existentes. No agregar frameworks ni modificar tablas.

**Scale/Scope**: Un listado de productos existente, con un máximo actual de 500 filas por consulta; los filtros se combinan y aplican en el servidor.

## Constitution Check

### Antes de la investigación

| Principio o gate | Estado | Evidencia y decisión |
|---|---|---|
| Arquitectura MVC en PHP | PASS | La lógica de filtrado pertenece a `models/Producto.php`; se conserva el flujo existente controller-vista. |
| Convenciones existentes | PASS | Se reutilizan `ProductoController`, `Producto`, la vista de productos y las rutas existentes. |
| Acceso seguro a MySQL | PASS | La búsqueda y filtros se incorporan a la consulta preparada existente, con parámetros enlazados. |
| Validación y escape | PASS | La vista valida los valores GET permitidos y sigue usando `e()` para toda salida dinámica. |
| Pruebas para funcionalidad nueva | PASS | PHPUnit como dependencia de desarrollo y pruebas automatizadas de la lógica del modelo forman parte del alcance. |
| Seguridad e integridad de datos | PASS | La funcionalidad es de lectura; no requiere cambios de esquema, escritura ni transacciones. |
| Compatibilidad | PASS | Se conservan el estado predeterminado, el orden, el límite de filas y los filtros actuales. |

No hay violaciones que requieran justificación.

## Project Structure

### Documentación de esta funcionalidad

```text
specs/001-busqueda-filtros-productos/
├── plan.md
├── research.md
├── data-model.md
├── quickstart.md
└── contracts/
    └── product-listing.md
```

### Código fuente y pruebas

```text
SistemaVentas-main/
├── composer.json                         # Añadir PHPUnit en require-dev
├── composer.lock                         # Resolver y fijar dependencia de desarrollo
├── phpunit.xml                            # Configuración de descubrimiento de pruebas
├── controllers/
│   └── ProductoController.php             # Se conserva la delegación a Producto::listar()
├── models/
│   └── Producto.php                       # Búsqueda y filtros mediante consulta preparada
├── views/
│   └── productos/
│       └── index.php                      # Controles, limpieza y resultados en el listado actual
└── tests/
    ├── bootstrap.php                      # Carga del modelo para las pruebas
    └── Unit/
        └── ProductoTest.php               # Casos de búsqueda y combinación de filtros
```

**Structure Decision**: Se amplía la aplicación MVC PHP existente en `SistemaVentas-main/`, sin crear otra aplicación ni nuevas capas. La página actual valida los criterios de entrada y llama al controlador; el modelo conserva la responsabilidad de construir y ejecutar la consulta. Las pruebas se agrupan bajo `tests/` dentro de la aplicación y usan la misma clase `Producto` con una base SQLite temporal en memoria.

## Constitution Check (post-design)

| Principio o gate | Estado | Revisión del diseño |
|---|---|---|
| Arquitectura MVC y convenciones | PASS | El diseño reutiliza las rutas y archivos MVC del módulo. |
| Consultas preparadas y validación | PASS | Todos los valores de búsqueda y filtros se validan y enlazan; no se concatenan valores externos al SQL. |
| Escape de salida | PASS | Los valores visibles continúan escapándose con `e()`. |
| Pruebas | PASS | El plan define pruebas automatizadas de la consulta, filtros combinados y comportamiento sin filtros. |
| Compatibilidad e integridad | PASS | No se cambia el esquema ni se altera la selección predeterminada o el orden del listado. |

No hay violaciones ni complejidad adicional que registrar.
