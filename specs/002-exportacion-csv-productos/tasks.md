# Tasks: Exportación CSV de productos

**Input**: Design documents in `specs/002-exportacion-csv-productos/`

**Prerequisites**: `plan.md`, `spec.md`, `research.md`, `data-model.md`, `quickstart.md`

**Tests**: PHPUnit tests are included as requested in the implementation plan. Existing product filter tests remain regression coverage.

**Organization**: Tasks are grouped by the two user stories in `spec.md`.

## Format: `- [ ] T### [P?] [Story?] Description with file path`

- **[P]**: The task can run in parallel with another task touching different files and having no incomplete prerequisite.
- **[Story]**: Traceability to a user story in `spec.md`.
- Every task includes an explicit file path.

## Phase 1: Setup

**Purpose**: Project initialization.

No setup task is needed: the PHP application, Composer dependencies, and PHPUnit configuration already exist.

---

## Phase 2: Foundational

**Purpose**: Blocking prerequisites shared by user stories.

No foundational code change is needed. The authenticated PHP application, filter normalizer, model query, permissions, and CSV output helper are reused.

---

## Phase 3: User Story 1 - Exportar productos filtrados (Priority: P1) — MVP

**Goal**: Download the exact products and order shown by the existing listing, with fields permitted for the current user.

**Independent Test**: Apply valid and invalid filters to the listing and export endpoint; compare resulting rows and order. Confirm role-dependent columns, formula-safe text, numeric precision, and the filename pattern/timezone.

### Tests for User Story 1

- [X] T001 [P] [US1] Add a combined invalid-filter PHPUnit regression case in `SistemaVentas-main/tests/Unit/ProductoFiltrosListadoTest.php`: submit non-text `q`, invalid category/state/availability, and an unaccepted `stock_bajo` value together; assert the normalized result is `q=''`, `categoria=0`, `estado='1'`, `disponibilidad=''`, and `stock_bajo=false`, matching the defaults the export controller passes to the listing query.
- [X] T002 [P] [US1] Add focused PHPUnit tests in `SistemaVentas-main/tests/Unit/ProductoExportacionCsvTest.php` for Spanish headers, row mapping, status labels, numeric precision, and the `productos.editar` cost rule; test formula safety for text beginning with `=`, `+`, `-`, and `@`; verify `nombreArchivoProductosCsv()` matches `productos_YYYY-MM-DD.csv` and uses the `America/Bogota` date for a fixed UTC instant crossing the local-date boundary.

### Implementation for User Story 1

- [X] T003 [US1] Implement permission-aware product row mapping and `nombreArchivoProductosCsv()` in `SistemaVentas-main/helpers/productos.php`, and extract the existing formula protection into a testable `protegerFilaCsvContraFormulas()` used by `exportarCsv()` in `SistemaVentas-main/helpers/reportes.php`; load the report helper from `SistemaVentas-main/tests/bootstrap.php` for unit coverage; follow `specs/002-exportacion-csv-productos/data-model.md` for `Export row.Código`, `Export row.Nombre`, `Export row.Categoría`, `Export row.Costo`, `Export row.Precio`, `Export row.Stock`, `Export row.Estado`, `Download metadata.File name`, `Download metadata.Encoding`, and `Download metadata.CSV columns and order`.
- [X] T004 [US1] Add `ProductoController::exportarListado()` in `SistemaVentas-main/controllers/ProductoController.php`; load the existing `helpers/funciones.php` and `helpers/reportes.php` dependencies, obtain active categories, normalize incoming GET values with `normalizarFiltrosListadoProductos()`, obtain rows through the same `listar()` path, include cost only for `productos.editar`, and stream mapped rows through `exportarCsv()`; use the canonical `specs/002-exportacion-csv-productos/data-model.md` definitions for `Filter set.q`, `Filter set.categoria`, `Filter set.estado`, `Filter set.disponibilidad`, and `Filter set.stock_bajo`.
- [X] T005 [US1] Add the thin authenticated endpoint `SistemaVentas-main/public/exportar_productos.php`; load `config/config.php`, `helpers/permisos.php`, and `controllers/ProductoController.php`, require `productos.ver`, instantiate `ProductoController`, and delegate `$_GET` to `exportarListado()` without duplicating filter, empty-result, mapping, or CSV response logic.
- [X] T006 [US1] Add only an “Exportar CSV” submit button to the current GET filter form in `SistemaVentas-main/views/productos/index.php`; target `APP_URL/public/exportar_productos.php` with `formaction`/`formmethod` so the existing filter controls are submitted, and do not add export processing or empty-result handling to the view.

**Checkpoint**: A user with `productos.ver` can export the same non-empty filtered listing, with the correct permission-dependent columns.

---

## Phase 4: User Story 2 - Recibir aviso cuando no hay coincidencias (Priority: P2)

**Goal**: Prevent empty CSV downloads and leave the user on the filtered listing with a clear message.

**Independent Test**: Request export for a filter combination with no matches; verify the visible message, no file download, and preserved normalized filter selections.

### Implementation for User Story 2

- [X] T007 [US2] Complete the empty-result branch in `ProductoController::exportarListado()` in `SistemaVentas-main/controllers/ProductoController.php` before any call to `exportarCsv()`; flash “No hay productos con estos filtros” and redirect to the listing with normalized filter values preserved, sending no download headers or CSV body, following `Download metadata.Empty result sets` in `specs/002-exportacion-csv-productos/data-model.md`.

**Checkpoint**: Empty results are handled by the controller and never start a CSV download.

---

## Phase 5: Polish & Cross-Cutting Validation

**Purpose**: Validate both user stories, security requirements, and existing listing behavior.

- [X] T008 Run `.\vendor\bin\phpunit --configuration phpunit.xml` from `SistemaVentas-main/` and complete every authorization, filter-parity (including invalid values), empty-result, formula-safety, Excel, and filename/timezone scenario in `specs/002-exportacion-csv-productos/quickstart.md`.

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No tasks; existing project setup is used.
- **Foundational (Phase 2)**: No tasks; existing permissions, normalizer, model query, and CSV helper are reused.
- **User Story 1 (Phase 3)**: Starts with the two independent test tasks; implementation follows both.
- **User Story 2 (Phase 4)**: Depends on the controller export method from T004; the empty check must precede streaming.
- **Polish (Phase 5)**: Depends on all preceding tasks.

### User Story Dependencies

- **User Story 1 (P1)**: Independent; delivers export for non-empty listing results.
- **User Story 2 (P2)**: Adds the empty-result branch to the controller export action from User Story 1.

### Task Dependency Graph

```text
T001 ----\
          -> T003 -> T004 -> T005 -> T006 -> T007 -> T008
T002 ----/
```

T001 and T002 can run in parallel because they update different test files. Remaining tasks are ordered so the controller, endpoint, presentation button, and empty-result branch are independently reviewable in story order.

## Parallel Example

```text
Run these independent test tasks in parallel:
T001: Add combined invalid-filter normalization test in tests/Unit/ProductoFiltrosListadoTest.php
T002: Add CSV/formula/filename tests in tests/Unit/ProductoExportacionCsvTest.php
```

## Implementation Strategy

### MVP First

1. Complete T001 and T002 as focused regression tests.
2. Complete T003 and T004 for safe mapping and the controller-owned non-empty export.
3. Complete T005 and T006 to expose the authenticated endpoint and button-only listing action.
4. Complete T007 so empty results are handled by the controller before streaming.
5. Complete T008 before release.

User Story 1 is the first demonstrable increment. The complete feature must also include User Story 2 before release.

### Incremental Delivery

1. Deliver the controller-backed export for non-empty filtered results.
2. Connect the button-only listing UI to the authenticated endpoint.
3. Add the controller-owned no-results flash and redirect.
4. Run PHPUnit and all end-to-end scenarios in `quickstart.md`.

## Notes

- No changes are planned for `SistemaVentas-main/models/Producto.php`, the database schema, or Composer dependencies.
- Reuse `normalizarFiltrosListadoProductos()` and `Producto::listar()` so CSV rows match the existing listing.
- The endpoint requires `productos.ver`; the controller includes cost only for users with `productos.editar`.
- Formula escaping remains the existing behavior; extracting its pure row transformation must not change serialized CSV output.
- **Format validation**: all eight tasks use `- [ ] T###`, user-story labels are present on story tasks only, parallel markers are limited to independent work, and every task includes a file path.
