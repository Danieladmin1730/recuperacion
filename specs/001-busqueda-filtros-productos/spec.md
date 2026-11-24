# Feature Specification: Búsqueda y filtros de productos

**Feature Branch**: `001-busqueda-filtros-productos`

**Created**: 2026-11-24

**Status**: Draft

**Input**: User description: "Funcionalidad: búsqueda y filtro de productos en el módulo de productos del Sistema de Ventas (código en SistemaVentas-main/). El usuario debe poder buscar productos escribiendo parte del nombre o del código, y filtrar el listado por categoría y por estado de stock (con stock / sin stock). Los resultados se muestran en el mismo listado existente, con un mensaje claro cuando no haya coincidencias, y un botón para limpiar los filtros. La búsqueda no distingue mayúsculas de minúsculas. No debe alterar el funcionamiento actual del listado cuando no se aplica ningún filtro."

## Clarifications

### Session 2026-11-24

- Q: ¿El filtro nuevo de disponibilidad (con stock/sin stock) debe añadirse conservando los filtros actuales de activo/inactivo y stock bajo? → A: Añadirlo como criterio independiente y conservar los filtros actuales de activo/inactivo y stock bajo.

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Encontrar productos por texto (Priority: P1)

El usuario escribe parte del nombre o del código de un producto y encuentra las coincidencias en el listado de productos existente, sin importar si usa mayúsculas o minúsculas.

**Why this priority**: La búsqueda por texto es la forma principal de localizar rápidamente un producto específico.

**Independent Test**: Con productos cuyo nombre y código sean conocidos, buscar fragmentos de ambos campos y comprobar que el listado muestra todas y solo las coincidencias.

**Acceptance Scenarios**:

1. **Given** que el listado contiene productos con distintos nombres, códigos y códigos de barras, **When** el usuario escribe un fragmento que aparece en cualquiera de esos campos, **Then** el producto aparece en los resultados; se conserva así la búsqueda existente por código de barras.
2. **Given** que existe un producto cuyo nombre, código o código de barras contiene el texto buscado, **When** el usuario busca ese texto con mayúsculas o minúsculas distintas, **Then** el producto aparece en los resultados.
3. **Given** que el texto buscado coincide con más de un campo del mismo producto, **When** se genera el listado, **Then** el producto aparece una sola vez.

---

### User Story 2 - Acotar productos por categoría y stock (Priority: P1)

El usuario restringe el listado a una categoría, a productos con stock o sin stock, o a una combinación de esos criterios y la búsqueda por texto.

**Why this priority**: Los filtros permiten localizar grupos de productos útiles para la gestión del catálogo y del inventario.

**Independent Test**: Aplicar cada criterio por separado y en combinación sobre un catálogo de prueba con categorías y cantidades de stock conocidas; verificar que cada resultado cumple todos los criterios seleccionados.

**Acceptance Scenarios**:

1. **Given** productos de varias categorías, **When** el usuario selecciona una categoría activa disponible en el módulo, **Then** el listado muestra únicamente productos de esa categoría.
2. **Given** productos con y sin unidades disponibles, **When** el usuario selecciona “con stock” o “sin stock”, **Then** el listado muestra únicamente productos que corresponden al estado elegido: stock mayor que cero o exactamente cero, respectivamente.
3. **Given** que el usuario ha introducido texto y seleccionado una categoría y un estado de stock, **When** se actualizan los resultados, **Then** solo aparecen productos que cumplen simultáneamente todos los criterios activos.
4. **Given** que ningún producto cumple los criterios activos, **When** se actualiza el listado, **Then** se muestra en el mismo listado un mensaje que indica que no hay productos que coincidan con los filtros y permanece visible la acción “Limpiar filtros”.
5. **Given** que el usuario combina disponibilidad con los filtros existentes de activo/inactivo o stock bajo, **When** se actualiza el listado, **Then** cada filtro permanece disponible y los resultados cumplen todos los criterios activos.

---

### User Story 3 - Limpiar filtros y conservar el listado actual (Priority: P2)

El usuario puede limpiar todos los criterios para volver al listado normal. Si no se ha aplicado ningún criterio, el listado mantiene su comportamiento actual.

**Why this priority**: La limpieza permite retomar la navegación habitual sin perder la continuidad del listado existente.

**Independent Test**: Comparar el listado sin filtros antes y después de usar la limpieza; verificar que el contenido y el comportamiento coinciden con el listado actual.

**Acceptance Scenarios**:

1. **Given** que uno o más criterios están activos, **When** el usuario pulsa “Limpiar filtros”, **Then** se vacía la búsqueda, se restablecen categoría, disponibilidad, estado del producto y stock bajo a sus valores predeterminados y se muestra el listado normal.
2. **Given** que no hay criterios activos, **When** el usuario abre el listado de productos, **Then** se conserva el contenido, orden y comportamiento actuales del listado.

### Edge Cases

- Una búsqueda vacía o compuesta únicamente por espacios, después de recortar espacios exteriores, no debe restringir los resultados; los demás filtros seleccionados siguen aplicándose.
- Si una categoría o un estado de stock no tiene productos coincidentes, debe mostrarse el mensaje de ausencia de resultados sin ocultar la opción de limpiar los filtros.
- Si los criterios seleccionados tienen coincidencias por separado pero ninguna satisface su intersección, se muestra el mismo estado vacío y la acción para limpiar.
- Limpiar filtros desde un resultado vacío o con resultados restaura los mismos valores predeterminados del listado.
- La búsqueda debe funcionar cuando el fragmento coincide con el nombre, el código o el código de barras, incluidos los casos en que coincide con varios campos del mismo producto.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: El usuario MUST poder buscar productos mediante un fragmento del nombre o del código y MUST conservar la búsqueda existente por código de barras. Se recortan los espacios al inicio y al final; la búsqueda vacía no restringe resultados y el texto se limita a 100 caracteres.
- **FR-002**: La búsqueda MUST ignorar las diferencias entre mayúsculas y minúsculas conforme a la intercalación existente de la base MySQL (`utf8mb4_unicode_ci`). La equivalencia de acentos y caracteres no ASCII sigue esa intercalación; no se introduce una regla de transliteración o normalización adicional.
- **FR-003**: El usuario MUST poder filtrar los productos por una categoría activa disponible en el módulo existente; la lista de opciones MUST corresponder a las categorías activas que devuelve dicho módulo.
- **FR-004**: El usuario MUST poder filtrar productos con stock (`stock > 0`) y sin stock (`stock = 0`). Un valor negativo de stock, si existiera en datos heredados, no corresponde a ninguna de esas dos opciones.
- **FR-005**: Cuando haya más de un criterio activo, el listado MUST mostrar solo productos que cumplan cada criterio activo. Las coincidencias de texto entre nombre, código y código de barras se combinan como alternativas; si coinciden varios campos del mismo producto, este aparece una sola vez.
- **FR-006**: Los resultados MUST aparecer en el listado de productos existente.
- **FR-007**: Si no hay productos que coincidan con los criterios activos, el listado MUST comunicar que no existen productos que coincidan con esos filtros y MUST mantener visible en el mismo listado una acción “Limpiar filtros”.
- **FR-008**: La acción para limpiar filtros MUST borrar el texto de búsqueda y restablecer categoría, disponibilidad, estado del producto y stock bajo a sus valores predeterminados, tanto desde un listado con resultados como desde uno vacío.
- **FR-009**: Cuando no haya filtros nuevos activos, el listado MUST conservar el comportamiento actual: mostrar productos activos por defecto (`estado = 1`), sin restricción de categoría o disponibilidad y con stock bajo desactivado; MUST conservar el orden actual (`estado` descendente y nombre ascendente) y el límite de 500 resultados.
- **FR-010**: El filtro de disponibilidad MUST añadirse sin reemplazar los filtros existentes de activo/inactivo y stock bajo.
- **FR-011**: Los criterios recibidos MUST validarse en el servidor. `q` se recorta y se limita a 100 caracteres; `categoria` acepta vacío o el identificador entero positivo de una categoría activa disponible; `estado` acepta `'1'`, `'0'` o `''`; `disponibilidad` acepta `con_stock`, `sin_stock` o `''`; `stock_bajo` acepta ausente o `'1'`. Un valor inválido MUST restablecer únicamente su criterio al valor predeterminado correspondiente: todas las categorías, estado activo, disponibilidad sin filtro o stock bajo desactivado.

### Key Entities *(include if feature involves data)*

- **Producto**: Elemento del catálogo con nombre, código, categoría y cantidad de stock, usado para presentar resultados.
- **Categoría**: Clasificación existente de productos, seleccionable como criterio de filtro.
- **Estado de stock**: Condición de un producto respecto a la disponibilidad de unidades: con stock o sin stock.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: En un catálogo de aceptación que incluya coincidencias solo por nombre, solo por código, solo por código de barras, por varios campos, sin coincidencia y con diferencias de mayúsculas, cada búsqueda devuelve el conjunto exacto de productos cuyo texto coincide con al menos uno de esos campos; el resultado es correcto en el 100% de los casos.
- **SC-002**: En un catálogo de aceptación con al menos dos categorías activas, productos con stock positivo y cero, estados activo e inactivo y valores de stock bajo y no bajo, el 100% de los resultados coincide exactamente con la intersección de los criterios seleccionados, incluida la búsqueda de texto cuando exista.
- **SC-003**: En todos los escenarios sin coincidencias, el listado comunica que no hay productos que coincidan con los criterios y mantiene disponible “Limpiar filtros” en ese mismo listado.
- **SC-004**: En todos los escenarios sin filtros nuevos, el listado conserva el estado predeterminado activo, el contenido permitido por ese estado, el orden por estado descendente y nombre ascendente, y el límite actual de 500 resultados.
- **SC-005**: El usuario puede restablecer todos los criterios con una sola acción y volver al listado normal.

## Assumptions

- “Con stock” significa que la cantidad disponible del producto es mayor que cero; “sin stock” significa que es cero.
- Cuando se combinan criterios, estos se aplican conjuntamente: un producto debe cumplirlos todos para aparecer.
- La lista de categorías ofrecida corresponde a las categorías activas que ya devuelve el módulo de productos.
- Los filtros existentes de activo/inactivo y stock bajo permanecen disponibles junto con el nuevo filtro de disponibilidad.
- Limpiar filtros restablece el listado existente a productos activos, todas las categorías, cualquier disponibilidad y stock bajo desactivado, sin cambiar el orden ni el límite actuales.
- El esquema existente define `stock` como obligatorio; cantidades negativas no son un estado de disponibilidad admitido y no se clasifican como “con stock” ni “sin stock”.
