# Research: Exportación CSV de productos

## Decisions

### Reuse the listing's normalized filters and model query

- **Decision**: The dedicated export endpoint delegates the raw GET filters to `ProductoController::exportarListado()`. The controller normalizes them with `normalizarFiltrosListadoProductos()` and obtains rows through the existing `ProductoController::listar()` / `Producto::listar()` path used by the HTML listing.
- **Rationale**: The model already applies all listing criteria, ordering, and the 500-row cap. Reusing the same normalizer and model query prevents filter or ordering drift without adding another SQL query implementation.
- **Alternatives considered**: Build a separate CSV SQL query; rejected because it duplicates filter logic and can export a different result set.

### Reuse the existing CSV streaming helper

- **Decision**: Reuse `exportarCsv()` from [helpers/reportes.php](../../SistemaVentas-main/helpers/reportes.php), which writes to `php://output`, sends CSV/download headers, writes a UTF-8 BOM, and uses `fputcsv()`. Extract its existing formula-prefix transformation into a pure helper so it can be tested independently, then keep `exportarCsv()` using that helper without changing output behavior.
- **Rationale**: This is established project behavior for Excel-compatible exports. Its semicolon delimiter matches the Spanish-locale CSV convention already used in the application.
- **Alternatives considered**: Add a second exporter specific to products; rejected as duplicate serialization and security behavior. Invoke the terminating `exportarCsv()` directly in a unit test; rejected because it sends headers and exits the request.

### Prepare product-specific headers and rows in a pure helper

- **Decision**: Add a focused product export mapping helper to [helpers/productos.php](../../SistemaVentas-main/helpers/productos.php). It returns Spanish headers and row arrays, with `Costo` included only when the user has `productos.editar`.
- **Rationale**: The mapping is specific to product fields and permission rules, and a pure helper can be tested without invoking a terminating HTTP download function.
- **Alternatives considered**: Put field mapping inline in the view; rejected because it makes permission-dependent mapping harder to unit-test and leaves the view with unnecessary transformation logic.

### Handle empty results before sending download headers

- **Decision**: `ProductoController::exportarListado()` checks the listing result before calling `exportarCsv()`. If empty, the controller sets the existing flash message and redirects to the product listing with normalized filters preserved. The endpoint adapter only enforces `productos.ver` and delegates; the listing view only renders the submit button.
- **Rationale**: The existing CSV helper sends headers and terminates the request; an empty-result check must therefore happen before invoking it. Redirecting keeps the ordinary listing and selected filters usable.
- **Alternatives considered**: Download a header-only file; rejected because the specification requires informing the user instead of downloading an empty result.

### Generate a testable, timezone-correct filename

- **Decision**: Add `nombreArchivoProductosCsv()` in `helpers/productos.php`. It formats `productos_YYYY-MM-DD.csv` using the application's configured timezone; allow a fixed instant to be supplied in tests so a UTC/Bogotá date-boundary case is deterministic.
- **Rationale**: A pure filename helper makes both the required filename shape and the configured local date independently verifiable without changing runtime timezone configuration.
- **Alternatives considered**: Test only that a downloaded filename contains today's date; rejected because that does not verify the exact pattern or the configured timezone at a date boundary.

### Preserve numeric precision and existing authorization

- **Decision**: Export price/cost at the product schema's two-decimal precision and stock at its three-decimal precision. Keep `productos.ver` as the export page gate and `productos.editar` as the independent cost-field gate.
- **Rationale**: The `productos` table stores `precio_compra` and `precio_venta` as `DECIMAL(12,2)` and `stock` as `DECIMAL(12,3)`. The current product list already hides cost unless `productos.editar` is granted.
- **Alternatives considered**: Apply the existing `numeroCsv()` formatting to stock; rejected because it rounds to two decimals and could lose valid stock precision.

## PHP Documentation

- `fputcsv()` formats an array of fields to an opened stream. PHP documents explicitly specifying the escape argument; since PHP 8.4, relying on its default is deprecated. The existing exporter explicitly passes an empty escape string. [PHP: fputcsv](https://www.php.net/manual/en/function.fputcsv.php)
- `php://output` is a write-only stream for output sent through PHP's output buffering mechanism. [PHP: php:// streams](https://www.php.net/manual/en/wrappers.php.php)
- `header()` must run before any body output. `Content-Disposition: attachment` supplies the suggested download filename. The dedicated endpoint must dispatch to the controller before emitting HTML. [PHP: header](https://www.php.net/manual/en/function.header.php)

## Repository Evidence

- Filter normalization and its supported criteria: [helpers/productos.php](../../SistemaVentas-main/helpers/productos.php).
- Filtered query, order, and 500-row cap: [models/Producto.php](../../SistemaVentas-main/models/Producto.php).
- Existing permission-aware cost column and filter form: [views/productos/index.php](../../SistemaVentas-main/views/productos/index.php).
- Existing CSV headers, UTF-8 BOM, semicolon delimiter, formula mitigation, and `fputcsv()` output: [helpers/reportes.php](../../SistemaVentas-main/helpers/reportes.php).
- Existing unit test patterns and SQLite in-memory fixtures: [tests/Unit/](../../SistemaVentas-main/tests/Unit/) and [ProductoTestDatabase.php](../../SistemaVentas-main/tests/Support/ProductoTestDatabase.php).
