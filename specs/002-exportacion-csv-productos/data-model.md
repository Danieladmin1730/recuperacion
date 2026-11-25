# Data Model: Exportación CSV de productos

This feature exports existing catalog data only. It adds no tables, columns, records, or persistent state.

## Export row

Each exported row represents one product returned by the existing filtered product listing, joined to its category.

| CSV field | Source value | Format / rule |
|---|---|---|
| Código | Product code | Text; preserve the value while applying the existing CSV helper's spreadsheet-formula safety handling. |
| Nombre | Product name | Text; preserve UTF-8 characters and CSV quoting. |
| Categoría | Joined category name | Text. |
| Costo | Purchase price | Include only when the current user has `productos.editar`; two decimal places. |
| Precio | Sale price | Two decimal places. |
| Stock | Current stock | Preserve up to three decimal places; do not round to the two-decimal price precision. |
| Estado | Product `estado` | Spanish label: `Activo` or `Inactivo`. |

For users with `productos.ver` but without `productos.editar`, omit the `Costo` header and value. For users with both permissions, include the `Costo` column between `Categoría` and `Precio`.

## Filter set

The export uses the normalized filter values already consumed by the product listing:

- `q`: trimmed, at most 100 characters; follows the existing name, code, and barcode search behavior.
- `categoria`: active category identifier or no category constraint.
- `estado`: active (`1`), inactive (`0`), or all (`''`), preserving the listing's active-by-default behavior.
- `disponibilidad`: `con_stock`, `sin_stock`, or no availability constraint.
- `stock_bajo`: boolean from the accepted query value.

The existing model query determines matching rows, ordering, and the maximum of 500 returned products. No independent export filter semantics are introduced.

## Download metadata

- File name: `productos_YYYY-MM-DD.csv`, using the export date.
- Encoding: UTF-8 with BOM for Excel compatibility.
- CSV columns and order follow the permission-dependent rules above.
- Empty result sets do not produce a download.
