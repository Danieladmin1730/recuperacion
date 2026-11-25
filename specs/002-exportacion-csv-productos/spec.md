# Feature Specification: Exportación CSV de productos

**Feature Branch**: `not-created`

**Created**: 2026-11-24

**Status**: Draft

**Input**: User description: "Desde el listado de productos, un usuario con permiso para ver productos puede descargar un archivo CSV con los productos que se muestran según los filtros aplicados: búsqueda por nombre o código, categoría, estado, disponibilidad y solo stock bajo. Sin filtros adicionales, exporta el listado visible por defecto. El archivo incluye código, nombre, categoría, costo, precio, stock y estado, encabezados en español, UTF-8 compatible con Excel y nombre con la fecha de exportación. Si no hay coincidencias, se informa al usuario en lugar de descargar un archivo vacío. El listado y sus filtros existentes no deben alterarse."

## Clarifications

### Session 2026-11-24

- Q: ¿Qué usuarios deben poder obtener el costo en el CSV de productos? → A: C: todos los usuarios con `productos.ver` pueden exportar; la columna Costo solo aparece para quienes también tienen `productos.editar`.

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Exportar productos filtrados (Priority: P1)

Como usuario que puede consultar productos, quiero descargar en CSV los productos del listado actual para revisar o compartir sus datos en una hoja de cálculo.

**Why this priority**: Permite reutilizar los resultados que el usuario ya obtuvo en el listado, sin tener que copiar los datos manualmente.

**Independent Test**: Con productos de distintas categorías, estados y existencias, aplicar uno o más filtros en el listado, exportar y verificar que el archivo contiene exactamente los resultados visibles y las columnas permitidas según los permisos del usuario. Repetir sin filtros para comprobar que se exporta el listado predeterminado.

**Acceptance Scenarios**:

1. **Given** que el usuario tiene permiso para ver productos y el listado presenta resultados, **When** exporta con uno o varios filtros activos, **Then** descarga un CSV con los mismos productos que muestra el listado y conserva los filtros en pantalla.
2. **Given** que el usuario tiene permiso para ver productos y no aplica filtros adicionales, **When** exporta, **Then** el archivo contiene todos los productos del listado predeterminado.
3. **Given** que el CSV se abre en Excel y hay datos con acentos, comas, comillas o separadores, **When** se revisan sus celdas, **Then** los encabezados y valores siguen legibles y cada dato permanece en su columna.
4. **Given** que un usuario no tiene permiso para ver productos, **When** intenta acceder a la exportación, **Then** no puede descargar el archivo.
5. **Given** que un usuario puede ver productos pero no tiene permiso para editar productos, **When** exporta el listado, **Then** el archivo no incluye la columna Costo ni su información; un usuario con ambos permisos sí obtiene esa columna.

---

### User Story 2 - Recibir aviso cuando no hay coincidencias (Priority: P2)

Como usuario que filtra el catálogo, quiero saber cuando ningún producto coincide para poder ajustar los filtros, en vez de recibir un CSV vacío.

**Why this priority**: Evita una descarga sin contenido que podría confundirse con un error o con un catálogo vacío.

**Independent Test**: Aplicar un criterio que no encuentre productos e iniciar la exportación; comprobar que no se descarga archivo, se muestra un aviso y el listado conserva los filtros aplicados.

**Acceptance Scenarios**:

1. **Given** que los filtros no encuentran productos, **When** el usuario solicita exportar, **Then** el sistema informa que no hay productos coincidentes y no descarga un archivo vacío.
2. **Given** que se mostró el aviso de resultados vacíos, **When** el usuario revisa el listado, **Then** los filtros aplicados siguen disponibles para modificarlos o limpiarlos.

### Edge Cases

- Los resultados incluyen valores con acentos, comillas, separadores o saltos de línea; deben conservarse como valores de celda y no desplazar las columnas.
- Cada filtro puede combinarse con los demás, y la exportación debe respetar la misma combinación que el listado.
- El nombre del archivo incluye la fecha en que se solicita la exportación, incluso cuando se repite una exportación en otra fecha.
- La búsqueda debe conservar el comportamiento existente del listado, incluido cualquier campo adicional que actualmente forme parte de esa búsqueda.
- El costo no debe aparecer en el CSV de usuarios que pueden ver productos pero no tienen permiso para editar productos.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: El listado de productos MUST ofrecer una acción para exportar los resultados actuales a CSV.
- **FR-002**: La exportación MUST estar disponible únicamente para usuarios con permiso para ver productos.
- **FR-003**: El conjunto de filas exportadas MUST coincidir con los resultados del listado para los filtros activos: búsqueda por nombre o código, categoría, estado, disponibilidad y solo stock bajo. MUST respetar también el comportamiento vigente de la búsqueda y el listado.
- **FR-004**: Cuando no se aplican filtros adicionales, la exportación MUST incluir el listado que el sistema presenta por defecto.
- **FR-005**: Cada fila del CSV MUST incluir código, nombre, categoría, precio, stock y estado, en ese orden. Para usuarios con permiso para editar productos, MUST incluir también el costo entre categoría y precio.
- **FR-006**: El CSV MUST usar encabezados en español. Para usuarios con permiso para editar productos, los encabezados MUST ser «Código», «Nombre», «Categoría», «Costo», «Precio», «Stock» y «Estado»; para usuarios sin ese permiso, MUST omitir «Costo».
- **FR-007**: El archivo descargado MUST tener codificación UTF-8 compatible con Excel y un nombre que incluya la fecha de exportación.
- **FR-008**: Si la búsqueda y los filtros no producen productos, el sistema MUST informar al usuario y MUST NOT descargar un archivo vacío.
- **FR-009**: La exportación MUST NOT modificar el listado, sus resultados ni sus filtros existentes.
- **FR-010**: Los usuarios con permiso para ver productos MUST poder exportar el listado. La columna y los valores de costo MUST aparecer únicamente para quienes también tengan permiso para editar productos.

### Key Entities *(include if feature involves data)*

- **Producto**: Artículo del catálogo con código, nombre, categoría, costo, precio, stock y estado; es el registro exportado.
- **Filtros del listado**: Criterios activos de búsqueda, categoría, estado, disponibilidad y stock bajo que determinan las filas exportadas.
- **Archivo CSV**: Representación descargable de los resultados del listado, con encabezados en español y fecha en su nombre.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: En el 100% de las combinaciones de filtros verificadas, el archivo contiene exactamente los mismos productos que el listado presenta para esos criterios.
- **SC-002**: El 100% de los archivos exportados contiene los encabezados y campos permitidos para el usuario: siete columnas, incluido el costo, para quien tiene permiso de edición; seis columnas sin costo para quien solo tiene permiso de consulta. Los valores con acentos y separadores se conservan correctamente al abrirse en Excel.
- **SC-003**: En el 100% de las pruebas sin coincidencias, no se descarga un CSV vacío, se informa al usuario y se mantienen los filtros aplicados.
- **SC-004**: La exportación no cambia los resultados, controles ni valores seleccionados del listado antes y después de descargar.

## Assumptions

- La exportación reutiliza los criterios y el conjunto de resultados que el listado muestra actualmente; no introduce filtros nuevos ni altera valores predeterminados.
- Se conserva la búsqueda existente del listado, aunque incluya otros campos además de nombre y código.
- La fecha del nombre del archivo se expresa como `YYYY-MM-DD`.
- El costo conserva la restricción del sistema actual: solo se exporta para usuarios con permiso para editar productos; el permiso para ver productos por sí solo no autoriza a recibir ese dato.
- Los permisos de consulta de productos y la sesión autenticada existentes siguen siendo la fuente de autorización.
