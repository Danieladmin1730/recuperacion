# Modelo de datos: Búsqueda y filtros de productos

**Fecha**: 2026-11-24  
**Especificación**: [spec.md](./spec.md)

## Entidades existentes

### Producto

Fila existente de `productos`; no se agregan ni modifican columnas.

| Campo | Uso en la funcionalidad | Regla |
|---|---|---|
| `id_producto` | Identidad del producto devuelto | Clave primaria existente |
| `nombre` | Búsqueda parcial | Se compara sin distinguir mayúsculas de minúsculas |
| `codigo` | Búsqueda parcial | Se compara sin distinguir mayúsculas de minúsculas |
| `codigo_barras` | Búsqueda parcial existente que se conserva | Puede ser nulo |
| `id_categoria` | Filtro de categoría | Debe corresponder a una categoría existente y seleccionable |
| `stock` | Estado derivado de disponibilidad | Mayor que cero = con stock; cero = sin stock |
| `stock_minimo` | Filtro existente de stock bajo | Se conserva con la condición actual `stock <= stock_minimo` |
| `estado` | Filtro existente activo/inactivo | Se conserva; el valor predeterminado actual es activo |

El producto mantiene su relación existente de pertenencia a una categoría. Los resultados siguen ordenándose y limitándose como en el listado actual.

### Categoría

Fila existente de `categorias`; el filtro utiliza su identificador y el listado conserva su etiqueta visible. No se modifican sus datos ni su relación con productos.

## Criterios de búsqueda y filtrado

Los valores de filtro son criterios temporales de la solicitud, no entidades persistentes:

| Criterio | Valor o forma | Regla de validación |
|---|---|---|
| Texto (`q`) | Texto opcional | Recortar espacios; permitir vacío; coincidencia parcial en nombre, código y código de barras |
| Categoría (`categoria`) | Identificador opcional | Entero positivo o “todas” |
| Estado del producto (`estado`) | Activo, inactivo o todos | Mantener los valores permitidos y el valor predeterminado actuales |
| Stock bajo (`stock_bajo`) | Activado/desactivado | Mantener el comportamiento y valor predeterminado actuales |
| Disponibilidad (`disponibilidad`) | `con_stock`, `sin_stock` o vacío | Aceptar solo esos valores; vacío significa no filtrar disponibilidad |

Cuando se usan varios criterios, el producto debe cumplirlos todos. Los campos que forman la búsqueda de texto se combinan como alternativas: basta una coincidencia en nombre, código o código de barras.

## Transiciones y persistencia

- No hay transiciones ni escritura de datos.
- No se necesita migración ni cambio de esquema.
- La acción de limpiar restablece los criterios al estado inicial que ya ofrece el listado.
