# Implementation Plan: Exportación CSV de productos

**Branch**: `002-exportacion-csv-productos` | **Date**: 2026-11-24 | **Spec**: [spec.md](./spec.md)

**Input**: Feature specification from `specs/002-exportacion-csv-productos/spec.md`

## Summary

Add a CSV download button to the existing products filter form. The button submits the current GET filter values to a thin authenticated endpoint at `public/exportar_productos.php`; that endpoint delegates to `ProductoController::exportarListado()`. The controller owns filter normalization, the existing `Producto::listar()` query, the no-results flash-and-redirect behavior, permission-aware mapping, filename generation, and the call to the existing CSV streaming helper. The products listing view only renders the button; it does not choose export behavior, prepare rows, or handle empty results.

The export uses the same normalized filter values and query as the listing, preserving row order and the existing 500-row limit. Users with `productos.ver` may export; the `Costo` column is included only with `productos.editar`.

## Technical Context

**Language/Version**: PHP 8.1+ (`composer.json` requires `^8.1`).

**Primary Dependencies**: Existing PHP MVC application, PDO, and PHPUnit 10.5; no added packages or frameworks.

**Storage**: Existing MySQL product and category tables through `config/database.php`; no schema changes or new persisted data.

**Testing**: PHPUnit 10.5 and `phpunit.xml`. Add focused tests for invalid filter normalization, CSV field/permission mapping, formula-safety serialization, and the exact filename pattern/timezone boundary. Retain the existing product filter suite and run the authenticated endpoint scenarios in `quickstart.md`.

**Target Platform**: Existing server-rendered PHP application, accessed in a browser; downloaded CSV is intended to open in Excel.

**Project Type**: Existing PHP MVC web application.

**Performance Goals**: Export the same result set the listing returns, currently capped at 500 products. No additional response-time target is specified.

**Constraints**: Preserve `normalizarFiltrosListadoProductos()` and `Producto::listar()` behavior, including default active status, ordering, barcode search, and the 500-row limit. Preserve semicolon-delimited UTF-8-BOM CSV output and formula-injection mitigation. Only users with `productos.editar` receive cost. Do not change listing controls, defaults, or database schema.

**Scale/Scope**: One listing and its existing maximum of 500 returned rows; one CSV file per explicit export request.

## Constitution Check

- **MVC and project conventions**: PASS after the revised boundary. `ProductoController::exportarListado()` owns export decisions and processing. `public/exportar_productos.php` is a thin authenticated HTTP adapter that invokes the controller. `views/productos/index.php` renders only the export button in the current filter form.
- **Database and data integrity**: PASS. Reuse the existing PDO-backed query and prepared statements; no writes, schema changes, or migrations.
- **Authentication and authorization**: PASS. The export endpoint requires `productos.ver` before invoking the controller. Cost is independently gated by `productos.editar`.
- **Compatibility and scope**: PASS. Preserve normalized filters, listing order/cap, and the regular HTML listing; no new dependencies or frameworks.
- **Testing and evidence**: PASS when the tasks and `quickstart.md` checks are completed. PHPUnit covers the explicit invalid-input, formula-safety, field/permission, and filename/timezone cases; the quickstart verifies filter parity, authorization, empty results, and Excel readability.

## Project Structure

### Documentation (this feature)

```text
specs/002-exportacion-csv-productos/
├── plan.md
├── research.md
├── data-model.md
└── quickstart.md
```

No `contracts/` directory is required: the feature adds no public API or system-to-system interface.

### Source Code

```text
SistemaVentas-main/
├── controllers/
│   └── ProductoController.php       # Own the export and empty-result flow
├── helpers/
│   ├── productos.php                # Product CSV mapping and filename
│   └── reportes.php                 # Reusable, testable formula-safety function and CSV output
├── public/
│   └── exportar_productos.php       # Thin permission-checked HTTP adapter
├── views/
│   └── productos/
│       └── index.php                # Render the export button only
└── tests/
    └── Unit/
        ├── ProductoExportacionCsvTest.php
        └── ProductoFiltrosListadoTest.php
```

**Structure Decision**: Keep request coordination and export behavior in `ProductoController`. The endpoint adapter performs the existing authentication/permission guard and delegates; it contains no filter, mapping, empty-result, or CSV serialization logic. The controller normalizes the incoming GET values with `normalizarFiltrosListadoProductos()`, queries the same listing through `Producto::listar()`, and handles empty results before any CSV headers are sent. It uses a product helper for permission-aware headers/rows and the date-based filename, and `exportarCsv()` for streaming. The listing view only adds a submit button whose `formaction` targets the endpoint and whose GET form carries the selected filters.
