# Quickstart: Validar exportación CSV de productos

## Prerequisites

- PHP 8.1 or later and the repository's existing Composer dependencies.
- A configured MySQL database and an authenticated user with `productos.ver`.
- A second test account (or role configuration) with `productos.ver` and `productos.editar` to validate cost visibility.
- Products that cover active/inactive states, multiple categories, stock/no stock, low stock, and accented or quoted text.

## Automated tests

From [SistemaVentas-main/](../../SistemaVentas-main), run the existing product unit suite:

```powershell
.\vendor\bin\phpunit --configuration phpunit.xml
```

Expected: existing search/filter/listing tests and the new product CSV mapping tests pass. The new tests should verify exact Spanish headers and field order for both permission levels, row mapping/status labels, and preservation of product values and stock precision.

## End-to-end scenarios

1. Sign in as a user with `productos.ver`; open the products listing.
2. Apply a text search, category, state, availability, and low-stock filter, individually and in combination. Choose a combination with results and export it.
3. Compare the CSV product codes and order against the rows shown on the page. Confirm the selected filters remain intact after download.
4. Repeat with no added filters and confirm the export matches the default listing (including its existing active-state default and 500-row cap).
5. As a `productos.ver`-only user, confirm the export is available but the `Costo` header and values are absent. As a user who also has `productos.editar`, confirm the `Costo` column appears between `Categoría` and `Precio`.
6. Apply filters with no matches and request export. Confirm a visible message appears, no file is downloaded, and the listing retains the filters.
7. Open a populated file in Excel. Confirm accented text and fields containing separators or quotes remain readable in their respective columns, the stock value retains three-decimal precision when needed, and the filename contains the export date.

## Expected outcome

The download contains exactly the same filtered product rows as the existing listing, with fields allowed by the user's permissions. No schema or listing-filter behavior changes.
