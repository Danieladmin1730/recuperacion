# Implementation Plan: Exportación CSV de productos

**Branch**: `002-exportacion-csv-productos` | **Date**: 2026-11-24 | **Spec**: [spec.md](./spec.md)

**Input**: Feature specification from `specs/002-exportacion-csv-productos/spec.md`

## Summary

Add a CSV download action to the existing products listing. Reuse its normalized filters and `Producto::listar()` result without introducing another query path, so exported product rows and ordering match the listing (including the existing 500-row cap). Stream the rows through the existing Excel-compatible CSV helper. Require `productos.ver` for the listing/export route and include cost only when the user also has `productos.editar`. If the filtered result is empty, notify the user and return to the listing with normalized filters intact instead of sending download headers.

## Technical Context

**Language/Version**: PHP 8.1+ (`composer.json` requires `^8.1`).

**Primary Dependencies**: Existing PHP MVC application, PDO, and PHPUnit 10.5; no added packages or frameworks.

**Storage**: Existing MySQL product and category tables through `config/database.php`; no schema changes or new persisted data.

**Testing**: PHPUnit 10.5, `phpunit.xml`, existing product filter tests, and focused unit tests for export row/header mapping. Follow with the manual end-to-end scenarios in `quickstart.md`.

**Target Platform**: Existing server-rendered PHP application, accessed in a browser; downloaded CSV is intended to open in Excel.

**Project Type**: Existing PHP MVC web application.

**Performance Goals**: Export the same result set the listing returns, currently capped at 500 products. No additional response-time target is specified.

**Constraints**: Preserve `normalizarFiltrosListadoProductos()` and `Producto::listar()` behavior, including default active status, ordering, barcode search, and the 500-row limit. Keep the current semicolon-delimited, UTF-8-BOM CSV format and formula-injection mitigation. Only users with `productos.editar` receive cost. Do not change listing controls, defaults, or database schema.

**Scale/Scope**: One listing page and its current maximum of 500 returned rows; one CSV file per explicit export request.

## Constitution Check

- **MVC and project conventions**: PASS. The existing listing view coordinates the request, `ProductoController::listar()` and `Producto::listar()` continue to provide rows, and product-specific CSV row preparation belongs with the existing product helpers. The existing report CSV helper handles output.
- **Database and data integrity**: PASS. Reuse the existing PDO-backed query and prepared statements; no writes, schema changes, or migrations.
- **Authentication and authorization**: PASS. The page already requires `productos.ver`; preserve that guard for the export request. Gate the cost field independently on `productos.editar`.
- **Compatibility and scope**: PASS. Preserve normalized filters, current listing result ordering and cap, and the regular HTML listing; no new dependencies or frameworks.
- **Testing and evidence**: PASS. Add focused PHPUnit coverage for the field/row mapping and cost-permission behavior; retain the existing filter suite and document end-to-end checks for download headers, empty results, filter retention, and Excel readability.

## Project Structure

### Documentation (this feature)

```text
specs/002-exportacion-csv-productos/
├── plan.md
├── research.md
├── data-model.md
└── quickstart.md
```

No `contracts/` directory is required: this feature adds no public API or system-to-system interface; it adds a download action to an authenticated internal page.

### Source Code

```text
SistemaVentas-main/
├── helpers/
│   ├── productos.php             # Add testable product CSV headers/row mapping
│   └── reportes.php              # Reuse existing CSV streaming helper unchanged
├── views/
│   └── productos/
│       └── index.php             # Add export action and handle download before HTML
├── controllers/
│   └── ProductoController.php    # Existing listar() wrapper reused unchanged
├── models/
│   └── Producto.php              # Existing listar() query reused unchanged
└── tests/
    └── Unit/
        └── ProductoExportacionCsvTest.php
```

**Structure Decision**: Extend the existing single-project MVC structure. The product list remains the source of truth for both display and export. A pure helper in `helpers/productos.php` prepares permission-aware headers and rows so they can be unit-tested. `views/productos/index.php` handles the GET export request before including the page layout and delegates CSV serialization/download headers to `exportarCsv()` in `helpers/reportes.php`. The controller, model, database schema, and dependencies do not need feature-specific changes.
