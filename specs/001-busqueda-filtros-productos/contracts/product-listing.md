# Contrato de interfaz: listado de productos

**Fecha**: 2026-11-24  
**Especificación**: [spec.md](../spec.md)

Este contrato describe la interacción web del listado existente. No se agrega una API externa.

## Entrada

El usuario autorizado para ver productos accede al listado por la navegación actual del sistema. El formulario GET existente conserva sus controles y añade:

| Control | Opciones / comportamiento |
|---|---|
| Buscar | Coincidencia parcial sin distinguir mayúsculas/minúsculas por nombre o código; se conserva la búsqueda parcial existente por código de barras |
| Categoría | Todas o una categoría disponible |
| Estado del producto | Se conserva el filtro actual: activos, inactivos o todos |
| Stock bajo | Se conserva la casilla actual |
| Disponibilidad | Todas, con stock o sin stock |
| Filtrar | Envía los criterios del formulario para actualizar el mismo listado |
| Limpiar filtros | Restablece búsqueda, categoría, disponibilidad y stock bajo; el estado del producto vuelve a su valor predeterminado actual |

Los valores de disponibilidad aceptados son `con_stock`, `sin_stock` y vacío. Los parámetros no reconocidos no deben interpretarse como una opción válida de disponibilidad.

## Respuesta observable

- Los resultados aparecen en la tabla de productos existente, junto con sus controles actuales y según los permisos existentes.
- Los criterios activos se aplican conjuntamente; una coincidencia en cualquiera de los campos de texto satisface el criterio textual.
- Si no hay coincidencias, se conserva el mensaje de lista vacía existente y se presenta una forma clara de limpiar los criterios.
- Sin criterios nuevos, se mantiene el listado por defecto actual, incluido estado, orden y límite de resultados.

## Seguridad y compatibilidad

- Se mantiene la comprobación de permiso `productos.ver`.
- Los valores de entrada se validan en el servidor y los valores usados en SQL se enlazan mediante PDO preparado.
- El texto dinámico presentado en la vista se escapa con `e()`.
- Los filtros no escriben datos y no cambian el esquema de base de datos.
