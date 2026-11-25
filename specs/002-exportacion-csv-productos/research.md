# Research: Exportación CSV de productos

## Decisions

### Reuse the listing's normalized filters and result query

- **Decision**: Normalize the incoming product filters with `normalizarFiltrosListadoProductos()` and pass that result to the same `ProductoController::listar()` call used for the HTML listing. Use that returned array for both rendering and export.
- **Rationale**: The model already applies all listing criteria, ordering, and the 500-row cap. Sharing the query result prevents filter or ordering drift and avoids a second, subtly different product query.
- **Alternatives considered**: Build a separate CSV SQL query; rejected because it duplicates filter logic and can export a different result set.

### Reuse the existing CSV streaming helper

- **Decision**: Reuse `exportarCsv()` from [helpers/reportes.php](../../SistemaVentas-main/helpers/reportes.php), which writes to `php://output`, sends CSV/download headers, writes a UTF-8 BOM, uses `fputcsv()`, and prefixes formula-like text values to reduce spreadsheet formula injection risk.
- **Rationale**: This is established project behavior for Excel-compatible exports. Its semicolon delimiter matches the Spanish-locale CSV convention already used in the application.
- **Alternatives considered**: Add a second exporter specific to products; rejected as duplicate serialization and security behavior.

### Prepare product-specific headers and rows in a pure helper

- **Decision**: Add a focused product export mapping helper to [helpers/productos.php](../../SistemaVentas-main/helpers/productos.php). It returns Spanish headers and row arrays, with `Costo` included only when the user has `productos.editar`.
- **Rationale**: The mapping is specific to product fields and permission rules, and a pure helper can be tested without invoking a terminating HTTP download function.
- **Alternatives considered**: Put field mapping inline in the view; rejected because it makes permission-dependent mapping harder to unit-test and leaves the view with unnecessary transformation logic.

### Handle empty results before sending download headers

- **Decision**: Check the listing result before calling `exportarCsv()`. If empty, show a user-facing flash message and redirect to the product listing with the normalized filters preserved.
- **Rationale**: The existing CSV helper sends headers and terminates the request; an empty-result check must therefore happen before invoking it. Redirecting keeps the ordinary listing and selected filters usable.
- **Alternatives considered**: Download a header-only file; rejected because the specification requires informing the user instead of downloading an empty result.

### Preserve numeric precision and existing authorization

- **Decision**: Export price/cost at the product schema's two-decimal precision and stock at its three-decimal precision. Keep `productos.ver` as the export page gate and `productos.editar` as the independent cost-field gate.
- **Rationale**: The `productos` table stores `precio_compra` and `precio_venta` as `DECIMAL(12,2)` and `stock` as `DECIMAL(12,3)`. The current product list already hides cost unless `productos.editar` is granted.
- **Alternatives considered**: Apply the existing `numeroCsv()` formatting to stock; rejected because it rounds to two decimals and could lose valid stock precision.

## PHP Documentation

- `fputcsv()` formats an array of fields to an opened stream. PHP documents explicitly specifying the escape argument; since PHP 8.4, relying on its default is deprecated. The existing exporter explicitly passes an empty escape string. [PHP: fputcsv](https://www.php.net/manual/en/function.fputcsv.php)
- `php://output` is a write-only stream for output sent through PHP's output buffering mechanism. [PHP: php:// streams](https://www.php.net/manual/en/wrappers.php.php)
- `header()` must run before any body output. `Content-Disposition: attachment` supplies the suggested download filename. The export path must therefore run before the product page includes its HTML layout. [PHP: header](https://www.php.net/manual/en/function.header.php)

## Repository Evidence

- Filter normalization and its supported criteria: [helpers/productos.php](../../SistemaVentas-main/helpers/productos.php).
- Filtered query, order, and 500-row cap: [models/Producto.php](../../SistemaVentas-main/models/Producto.php).
- Existing permission-aware cost column and filter form: [views/productos/index.php](../../SistemaVentas-main/views/productos/index.php).
- Existing CSV headers, UTF-8 BOM, semicolon delimiter, formula mitigation, and `fputcsv()` output: [helpers/reportes.php](../../SistemaVentas-main/helpers/reportes.php).
- Existing unit test patterns and SQLite in-memory fixtures: [tests/Unit/](../../SistemaVentas-main/tests/Unit/) and [ProductoTestDatabase.php](../../SistemaVentas-main/tests/Support/ProductoTestDatabase.php).
