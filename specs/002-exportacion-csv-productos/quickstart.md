# Quickstart: Validar exportación CSV de productos

## Prerequisites

- PHP 8.1 or later and the repository's existing Composer dependencies.
- A configured MySQL database and an authenticated user with `productos.ver`.
- A test account without `productos.ver` to verify the export endpoint denies access.
- A second test account (or role configuration) with `productos.ver` and `productos.editar` to validate cost visibility.
- Products that cover active/inactive states, multiple categories, stock/no stock, low stock, and accented or quoted text.

## Automated tests

From [SistemaVentas-main/](../../SistemaVentas-main), run the existing product unit suite:

```powershell
.\vendor\bin\phpunit --configuration phpunit.xml
```

Expected: existing search/filter/listing tests and the new product CSV tests pass. Tests cover exact Spanish headers and field order for both permission levels, row mapping/status labels, numeric precision, formula-like text, invalid filter normalization, and the exact filename pattern and configured timezone.

## End-to-end scenarios

1. Sign in as a user with `productos.ver`; open the products listing.
2. Apply a text search, category, state, availability, and low-stock filter, individually and in combination. Choose a combination with results and export it.
3. Submit invalid filter values to `public/exportar_productos.php` (for example `q[]=texto`, an inactive/invalid category, `estado=otro`, `disponibilidad=otro`, and `stock_bajo=true`). Confirm the controller normalizes them exactly like the listing and the exported rows match the resulting listing.
4. Compare the CSV product codes and order against the rows shown on the page. Confirm the selected filters remain intact after download.
5. Repeat with no added filters and confirm the export matches the default listing (including its existing active-state default and 500-row cap).
6. As a `productos.ver`-only user, confirm the export is available but the `Costo` header and values are absent. As a user who also has `productos.editar`, confirm the `Costo` column appears between `Categoría` and `Precio`.
7. As a user without `productos.ver`, request `public/exportar_productos.php` directly. Confirm access is denied and no CSV is downloaded.
8. Apply filters with no matches and request export. Confirm a visible message appears, no file is downloaded, and the listing retains the normalized filter selections.
9. Open a populated file in Excel. Confirm accented text and fields containing separators or quotes remain readable in their respective columns, and stock retains its stored precision.
10. Verify the filename exactly matches `productos_YYYY-MM-DD.csv`. In the PHPUnit date-boundary test, pass a fixed UTC instant that falls on the previous calendar date in `America/Bogota` and assert the filename uses the Bogotá date.
11. Run the formula-safety PHPUnit test with text values beginning with `=`, `+`, `-`, and `@`; confirm the serialized values are protected as text and are not interpreted as formulas.

## Expected outcome

The download contains exactly the same filtered product rows as the existing listing, with fields allowed by the user's permissions. No schema or listing-filter behavior changes.
