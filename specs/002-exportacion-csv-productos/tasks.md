# Tasks: Exportación CSV de productos

**Input**: Design documents in `specs/002-exportacion-csv-productos/`

**Prerequisites**: `plan.md`, `spec.md`, `research.md`, `data-model.md`, `quickstart.md`

**Tests**: PHPUnit tests are included as requested in the implementation plan. Existing filter tests remain regression coverage.

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

No foundational code change is needed. The authenticated product listing, permission checks, filter normalizer, model query, and CSV output helper are already present and will be reused.

---

## Phase 3: User Story 1 - Exportar productos filtrados (Priority: P1) — MVP

**Goal**: Download the exact products and order shown by the existing listing, with fields permitted for the current user.

**Independent Test**: Apply the default listing and representative search/category/state/availability/low-stock filters; compare exported rows and order with the page. Confirm role-dependent headers and values.

### Tests for User Story 1

- [ ] T001 [US1] Add PHPUnit unit tests for Spanish CSV headers, row mapping, status labels, numeric precision, and the `productos.editar` cost-field rule in `SistemaVentas-main/tests/Unit/ProductoExportacionCsvTest.php`.

### Implementation for User Story 1

- [ ] T002 [US1] Add a testable product CSV mapping helper in `SistemaVentas-main/helpers/productos.php`; follow the canonical definitions in `specs/002-exportacion-csv-productos/data-model.md` for `Export row.Código`, `Export row.Nombre`, `Export row.Categoría`, `Export row.Costo`, `Export row.Precio`, `Export row.Stock`, and `Export row.Estado`, including permission-dependent cost and empty-result behavior.
- [ ] T003 [US1] Add the “Exportar CSV” action and non-empty download path in `SistemaVentas-main/views/productos/index.php`; reuse the normalized filters and the existing `$productos` result, preserve filter parameters, require `productos.ver`, gate cost on `productos.editar`, and call the existing `exportarCsv()` helper from `SistemaVentas-main/helpers/reportes.php` before page-layout output.

**Checkpoint**: A user with `productos.ver` can download the currently listed results with the correct permission-dependent columns.

---

## Phase 4: User Story 2 - Recibir aviso cuando no hay coincidencias (Priority: P2)

**Goal**: Prevent empty CSV downloads and leave the user on the filtered listing with a clear message.

**Independent Test**: Request export for a filter combination with no matches; verify the visible “No hay productos con estos filtros” message, no file download, and preserved filter selections.

### Tests for User Story 2

- [ ] T004 [P] [US2] Add a PHPUnit test for the empty product-result case in `SistemaVentas-main/tests/Unit/ProductoExportacionCsvVaciaTest.php`, asserting that product export mapping produces no downloadable payload for an empty result.

### Implementation for User Story 2

- [ ] T005 [US2] Handle empty export results in `SistemaVentas-main/views/productos/index.php` before calling `exportarCsv()`; flash the existing no-results message and redirect to the listing with normalized filters preserved, without sending download headers or a CSV body.

**Checkpoint**: An empty result gives feedback in the listing and never starts a file download.

---

## Phase 5: Polish & Cross-Cutting Validation

**Purpose**: Validate compatibility across both stories without changing existing list behavior.

- [ ] T006 Run `.\vendor\bin\phpunit --configuration phpunit.xml` from `SistemaVentas-main/` and complete the role, filter-parity, empty-result, and Excel scenarios in `specs/002-exportacion-csv-productos/quickstart.md`.

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No tasks; existing project setup is used.
- **Foundational (Phase 2)**: No tasks; existing product listing, permissions, and CSV helper are reused.
- **User Story 1 (Phase 3)**: Starts immediately; no new schema or infrastructure prerequisite.
- **User Story 2 (Phase 4)**: Depends on the export request path from User Story 1 (T003); it adds the empty-result behavior to that path.
- **Polish (Phase 5)**: Depends on both user stories being complete.

### User Story Dependencies

- **User Story 1 (P1)**: Independent; delivers export for non-empty listing results.
- **User Story 2 (P2)**: Depends on the export path and mapping introduced by User Story 1.

### Task Dependency Graph

```text
T001 -> T002 -> T003 ----\
                  \       -> T005 -> T006
                   -> T004 /
```

T003 and T004 can run in parallel after T002 because they modify different files. All other tasks are sequential due to test-first implementation or shared-file changes.

## Parallel Example: User Story 1 and User Story 2

After T002 completes, the successful download path and the empty-result unit test can proceed in parallel:

```text
T003: Integrate the non-empty export path in SistemaVentas-main/views/productos/index.php
T004: Add the empty-result mapping test in SistemaVentas-main/tests/Unit/ProductoExportacionCsvVaciaTest.php
```

## Implementation Strategy

### MVP First

1. Complete T001 and T002 for testable CSV row/header mapping.
2. Complete T003 for the permission-aware, filtered, non-empty download.
3. Complete T004 and T005 before release so empty filters never produce a blank download.
4. Complete T006 and verify both stories independently.

User Story 1 is the first demonstrable increment. The complete feature must include User Story 2 before release because it defines the required empty-result behavior.

### Incremental Delivery

1. Deliver filtered export for non-empty results (User Story 1).
2. Add the no-results feedback and download suppression (User Story 2).
3. Run PHPUnit and the end-to-end scenarios in `quickstart.md`.

## Notes

- No changes are planned for `SistemaVentas-main/models/Producto.php`, `SistemaVentas-main/controllers/ProductoController.php`, the database schema, or Composer dependencies.
- Reuse `normalizarFiltrosListadoProductos()` and `Producto::listar()` so CSV rows match the existing listing.
- The cost field remains protected by `productos.editar`; `productos.ver` alone grants export access without cost.
- **Format validation**: all six task entries use `- [ ] T###`, user-story labels are present only for story tasks, parallel markers are limited to independent work, and every task includes a file path.
