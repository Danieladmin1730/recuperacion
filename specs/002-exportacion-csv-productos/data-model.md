# Data Model: Exportación CSV de productos

This feature exports existing catalog data only. It adds no tables, columns, records, or persistent state.

## Export row

Each exported row represents one product returned by the existing filtered product listing, joined to its category.

| Canonical field (`Entity.field`) | Source value | Format / rule |
|---|---|---|
| `Export row.Código` | Product code | Text; preserve the value while applying the existing CSV helper's spreadsheet-formula safety handling. |
| `Export row.Nombre` | Product name | Text; preserve UTF-8 characters and CSV quoting. |
| `Export row.Categoría` | Joined category name | Text. |
| `Export row.Costo` | Purchase price | Include only when the current user has `productos.editar`; two decimal places. |
| `Export row.Precio` | Sale price | Two decimal places. |
| `Export row.Stock` | Current stock | Preserve up to three decimal places; do not round to the two-decimal price precision. |
| `Export row.Estado` | Product `estado` | Spanish label: `Activo` or `Inactivo`. |

For users with `productos.ver` but without `productos.editar`, omit the `Costo` header and value. For users with both permissions, include the `Costo` column between `Categoría` and `Precio`.

## Filter set

The export controller uses the same normalizer and model query as the product listing:

| Canonical field (`Entity.field`) | Format / rule |
|---|---|
| `Filter set.q` | Trimmed, at most 100 characters; follows the existing name, code, and barcode search behavior. |
| `Filter set.categoria` | Active category identifier or no category constraint. |
| `Filter set.estado` | Active (`1`), inactive (`0`), or all (`''`), preserving the listing's active-by-default behavior. |
| `Filter set.disponibilidad` | `con_stock`, `sin_stock`, or no availability constraint. |
| `Filter set.stock_bajo` | Boolean from the accepted query value. |

The existing model query determines matching rows, ordering, and the maximum of 500 returned products. No independent export filter semantics are introduced.

## Download metadata

| Canonical field (`Entity.field`) | Format / rule |
|---|---|
| `Download metadata.File name` | `productos_YYYY-MM-DD.csv`, using the current date in the application's configured timezone (`America/Bogota`). |
| `Download metadata.Encoding` | UTF-8 with BOM for Excel compatibility; semicolon-delimited CSV. |
| `Download metadata.CSV columns and order` | Follow the permission-dependent `Export row` field order above. |
| `Download metadata.Empty result sets` | Do not produce a download; the controller informs the user and redirects to the filtered listing. |
