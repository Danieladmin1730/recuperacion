---
description: "Tareas de implementación para búsqueda y filtros de productos"
---

# Tasks: Búsqueda y filtros de productos

**Input**: Documentos de diseño en `specs/001-busqueda-filtros-productos/`

**Prerequisitos**: `plan.md`, `spec.md`, `research.md`, `data-model.md`, `contracts/product-listing.md`, `quickstart.md`

**Organización**: Tareas agrupadas por historia de usuario para implementar y validar incrementos independientes.

**Pruebas**: Incluidas porque se solicitaron pruebas automatizadas del modelo con PHPUnit.

## Formato

Cada tarea usa `- [ ] T### [P?] [US#?] Descripción con rutas de archivo`. `[P]` identifica tareas que pueden ejecutarse en paralelo; la etiqueta `[US#]` aparece únicamente en tareas de historias.

## Phase 1: Setup

**Purpose**: Preparar PHPUnit como herramienta de desarrollo y configurar la ejecución de pruebas.

- [X] T001 Añadir `phpunit/phpunit` `^10.5` en `require-dev` de `SistemaVentas-main/composer.json` y actualizar `SistemaVentas-main/composer.lock` mediante Composer, sin modificar dependencias de producción.
- [X] T002 [P] Crear `SistemaVentas-main/phpunit.xml` para descubrir las pruebas de `SistemaVentas-main/tests/Unit/` y crear `SistemaVentas-main/tests/bootstrap.php` para cargar el modelo requerido por la suite.

## Phase 2: Foundational

**Purpose**: Proveer datos de prueba reutilizables para las pruebas del modelo antes de implementar las historias.

- [X] T003 Crear `SistemaVentas-main/tests/Support/ProductoTestDatabase.php` con PDO SQLite en memoria, esquema mínimo de categorías y productos, datos semilla deterministas y un modo de comparación sensible a mayúsculas para verificar la búsqueda insensible a mayúsculas.

## Phase 3: User Story 1 - Encontrar productos por texto (Priority: P1) — MVP

**Goal**: Buscar fragmentos de nombre o código sin distinguir mayúsculas/minúsculas y conservar la búsqueda existente por código de barras.

**Independent Test**: Ejecutar `ProductoBusquedaTest` contra SQLite en memoria; comprobar que fragmentos de cada campo devuelven todos y solo los productos coincidentes independientemente del uso de mayúsculas.

### Tests for User Story 1

- [X] T004 [US1] Crear `SistemaVentas-main/tests/Unit/ProductoBusquedaTest.php` con pruebas PHPUnit de coincidencia parcial y de mayúsculas/minúsculas; cubrir las reglas de `FiltroListado.q` y los campos `Producto.nombre`, `Producto.codigo` y `Producto.codigo_barras` definidos en `data-model.md`.

### Implementation for User Story 1

- [X] T005 [US1] Actualizar la búsqueda `q` en `SistemaVentas-main/models/Producto.php` para garantizar coincidencia parcial sin distinguir mayúsculas/minúsculas, mantener la búsqueda por `Producto.nombre`, `Producto.codigo` y `Producto.codigo_barras` y conservar parámetros PDO preparados, según `FiltroListado.q` en `data-model.md`.

**Checkpoint**: La búsqueda por texto pasa sus pruebas de forma independiente y no altera los criterios distintos de texto.

## Phase 4: User Story 2 - Acotar productos por categoría y stock (Priority: P1)

**Goal**: Filtrar por categoría y disponibilidad, combinando todos los criterios existentes y mostrando una acción para limpiar si no hay resultados.

**Independent Test**: Ejecutar pruebas de `ProductoDisponibilidadTest` y comprobar en el listado que cada resultado satisface categoría, disponibilidad, estado y stock bajo; un conjunto sin coincidencias presenta mensaje y acción de limpieza.

### Tests for User Story 2

- [X] T006 [US2] Crear `SistemaVentas-main/tests/Unit/ProductoDisponibilidadTest.php` con pruebas PHPUnit para categoría, productos con/sin stock y combinación de criterios; cubrir `FiltroListado.categoria`, `FiltroListado.disponibilidad`, `FiltroListado.estado`, `FiltroListado.stock_bajo` y los campos `Producto.id_categoria`, `Producto.stock`, `Producto.estado`, `Producto.stock_minimo` de `data-model.md`.

### Implementation for User Story 2

- [X] T007 [P] [US2] Ampliar `Producto::listar()` en `SistemaVentas-main/models/Producto.php` para aceptar solo `FiltroListado.disponibilidad` (`con_stock`, `sin_stock` o vacío), aplicar la condición a `Producto.stock` y combinarla con `FiltroListado.categoria`/`Producto.id_categoria`, `FiltroListado.estado`/`Producto.estado` y `FiltroListado.stock_bajo`/`Producto.stock_minimo`, según `data-model.md`, sin sustituir los filtros existentes.
- [X] T008 [P] [US2] Validar el parámetro GET de disponibilidad e incorporar el selector “Todas / Con stock / Sin stock” al formulario de `SistemaVentas-main/views/productos/index.php`; conservar las reglas de `FiltroListado.q`, `FiltroListado.categoria`, `FiltroListado.estado`, `FiltroListado.stock_bajo` y `FiltroListado.disponibilidad`, y mostrar las opciones con `Categoria.id_categoria`, `Categoria.nombre` y `Categoria.estado`, según `data-model.md`.
- [X] T009 [US2] En `SistemaVentas-main/views/productos/index.php`, presentar un mensaje claro cuando no haya resultados y ofrecer desde ese estado una acción que restablezca los criterios a sus valores predeterminados.

**Checkpoint**: Categoría, stock y criterios anteriores se combinan correctamente; la lista vacía comunica el resultado y permite recuperarse.

## Phase 5: User Story 3 - Limpiar filtros y conservar el listado actual (Priority: P2)

**Goal**: Permitir limpiar todos los criterios desde cualquier resultado y restaurar el listado predeterminado sin cambiar el comportamiento existente.

**Independent Test**: Con resultados y sin ellos, activar varios criterios, limpiar y comprobar que el texto y filtros nuevos desaparecen, que el estado del producto vuelve a su valor predeterminado y que el listado recupera contenido y orden originales.

### Tests for User Story 3

- [X] T010 [US3] Crear `SistemaVentas-main/tests/Unit/ProductoListadoPredeterminadoTest.php` con una prueba de regresión de `Producto::listar()` que compruebe el orden y resultados con los criterios actuales, sin disponibilidad nueva; cubrir `Producto.estado`, `Producto.nombre`, `Producto.stock_minimo` y los valores predeterminados de `FiltroListado.estado` y `FiltroListado.stock_bajo` en `data-model.md`.

### Implementation for User Story 3

- [X] T011 [US3] En `SistemaVentas-main/views/productos/index.php`, hacer que “Limpiar filtros” esté disponible con resultados y sin ellos y restablezca búsqueda, categoría, disponibilidad, estado y stock bajo a los valores predeterminados de `FiltroListado.q`, `FiltroListado.categoria`, `FiltroListado.disponibilidad`, `FiltroListado.estado` y `FiltroListado.stock_bajo` en `data-model.md`; conservar las filas y acciones actuales identificadas por `Producto.id_producto` y escapar la salida con `e()`.

**Checkpoint**: Limpiar filtros desde cualquier estado restaura el listado normal y los criterios previos siguen disponibles.

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Validar el conjunto completo contra el contrato y la guía de ejecución.

- [X] T012 Ejecutar PHPUnit desde `SistemaVentas-main/` y completar los escenarios de `specs/001-busqueda-filtros-productos/quickstart.md`; registrar en ese archivo cualquier resultado o requisito de ejecución que haya cambiado.

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: T001 y T002 no dependen entre sí.
- **Foundational (Phase 2)**: T003 depende de completar T001 y T002; bloquea las pruebas de todas las historias.
- **User Story 1 (Phase 3)**: Depende de T003.
- **User Story 2 (Phase 4)**: Depende de completar US1 porque modifica la misma consulta del modelo y amplía el mismo formulario de listado.
- **User Story 3 (Phase 5)**: Depende de completar US2 para limpiar también el nuevo selector de disponibilidad.
- **Polish (Phase 6)**: T012 depende de las tres historias implementadas.

### User Story Dependencies

- **US1 (P1)**: Tras Setup y Foundational; no depende de otras historias.
- **US2 (P1)**: Tras US1 por compartir `Producto::listar()` y la vista de productos.
- **US3 (P2)**: Tras US2 para limpiar y restablecer todos los criterios del formulario.

### Parallel Opportunities

- T001 y T002 pueden ejecutarse en paralelo: modifican `composer.json`/`composer.lock` y la configuración de PHPUnit en archivos distintos.
- Tras completar T006, T007 y T008 pueden ejecutarse en paralelo porque modifican el modelo y la vista respectivamente; T009 debe seguir a T008 por editar la misma vista.
- No se recomienda paralelizar historias: sus cambios se solapan en la consulta del modelo y el formulario de listado.

## Parallel Example: User Story 2

Después de completar T006, se puede trabajar en paralelo:

```text
T007 -> SistemaVentas-main/models/Producto.php
T008 -> SistemaVentas-main/views/productos/index.php
```

Después se completa T009 en la vista, seguido de la validación independiente de la historia.

## Implementation Strategy

### MVP

1. Completar Setup y Foundational.
2. Completar US1: pruebas y búsqueda textual parcial insensible a mayúsculas.
3. Ejecutar sus pruebas y validar el listado existente.

US1 es el MVP técnico más pequeño; para completar el alcance funcional solicitado se deben entregar también US2 y US3.

### Entrega incremental

1. Integrar Setup y Foundational antes de empezar con cambios del producto.
2. Entregar US1 y comprobar que se conserva la búsqueda actual.
3. Entregar US2 y probar criterios individuales y combinados, incluyendo el estado vacío.
4. Entregar US3 y comprobar la restauración del listado predeterminado.
5. Ejecutar la validación completa descrita en `quickstart.md`.

## Notes

- `[P]` indica archivos distintos y ausencia de dependencias entre las tareas paralelas.
- Cada tarea de historia lleva su etiqueta `[US1]`, `[US2]` o `[US3]`.
- Las tareas de prueba deben preceder a la implementación correspondiente.
- Las restricciones de campos se citan desde `data-model.md` en las tareas que las implementan.

## Phase 7: Convergence

- [X] T013 [CRITICAL] Validar estrictamente los parámetros GET de productos según FR-011: manejar `q` no textual sin error, aceptar solo categorías activas con identificador entero positivo y limitar `stock_bajo` a ausente o `'1'`; conservar los defaults definidos para estado y disponibilidad y añadir pruebas de regresión para valores inválidos. per FR-011, Constitution IV (contradicts)
- [X] T014 Completar la comprobación visual autenticada del listado siguiendo `quickstart.md`: validar filtros combinados, mensaje y acción sin coincidencias, y restauración del listado con “Limpiar filtros”; registrar el resultado. per SC-003, SC-005, T012 (partial)
