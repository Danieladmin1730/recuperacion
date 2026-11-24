# Feature Specification: Búsqueda y filtros de productos

**Feature Branch**: `001-busqueda-filtros-productos`

**Created**: 2026-11-24

**Status**: Draft

**Input**: User description: "Funcionalidad: búsqueda y filtro de productos en el módulo de productos del Sistema de Ventas (código en SistemaVentas-main/). El usuario debe poder buscar productos escribiendo parte del nombre o del código, y filtrar el listado por categoría y por estado de stock (con stock / sin stock). Los resultados se muestran en el mismo listado existente, con un mensaje claro cuando no haya coincidencias, y un botón para limpiar los filtros. La búsqueda no distingue mayúsculas de minúsculas. No debe alterar el funcionamiento actual del listado cuando no se aplica ningún filtro."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Encontrar productos por texto (Priority: P1)

El usuario escribe parte del nombre o del código de un producto y encuentra las coincidencias en el listado de productos existente, sin importar si usa mayúsculas o minúsculas.

**Why this priority**: La búsqueda por texto es la forma principal de localizar rápidamente un producto específico.

**Independent Test**: Con productos cuyo nombre y código sean conocidos, buscar fragmentos de ambos campos y comprobar que el listado muestra todas y solo las coincidencias.

**Acceptance Scenarios**:

1. **Given** que el listado contiene productos con distintos nombres y códigos, **When** el usuario escribe un fragmento que aparece en el nombre, **Then** el listado muestra los productos cuyo nombre contiene ese fragmento.
2. **Given** que el listado contiene productos con distintos códigos, **When** el usuario escribe un fragmento de un código, **Then** el listado muestra los productos cuyo código contiene ese fragmento.
3. **Given** que existe un producto cuyo nombre o código contiene el texto buscado, **When** el usuario busca ese texto con mayúsculas o minúsculas distintas, **Then** el mismo producto aparece en los resultados.

---

### User Story 2 - Acotar productos por categoría y stock (Priority: P1)

El usuario restringe el listado a una categoría, a productos con stock o sin stock, o a una combinación de esos criterios y la búsqueda por texto.

**Why this priority**: Los filtros permiten localizar grupos de productos útiles para la gestión del catálogo y del inventario.

**Independent Test**: Aplicar cada criterio por separado y en combinación sobre un catálogo de prueba con categorías y cantidades de stock conocidas; verificar que cada resultado cumple todos los criterios seleccionados.

**Acceptance Scenarios**:

1. **Given** productos de varias categorías, **When** el usuario selecciona una categoría, **Then** el listado muestra únicamente productos de esa categoría.
2. **Given** productos con y sin unidades disponibles, **When** el usuario selecciona “con stock” o “sin stock”, **Then** el listado muestra únicamente productos que corresponden al estado elegido.
3. **Given** que el usuario ha introducido texto y seleccionado una categoría y un estado de stock, **When** se actualizan los resultados, **Then** solo aparecen productos que cumplen simultáneamente todos los criterios activos.
4. **Given** que ningún producto cumple los criterios activos, **When** se actualiza el listado, **Then** se muestra un mensaje claro de que no hay productos coincidentes y se ofrece la acción para limpiar los filtros.

---

### User Story 3 - Limpiar filtros y conservar el listado actual (Priority: P2)

El usuario puede limpiar todos los criterios para volver al listado normal. Si no se ha aplicado ningún criterio, el listado mantiene su comportamiento actual.

**Why this priority**: La limpieza permite retomar la navegación habitual sin perder la continuidad del listado existente.

**Independent Test**: Comparar el listado sin filtros antes y después de usar la limpieza; verificar que el contenido y el comportamiento coinciden con el listado actual.

**Acceptance Scenarios**:

1. **Given** que uno o más criterios están activos, **When** el usuario pulsa “Limpiar filtros”, **Then** se vacía la búsqueda, se restablecen los filtros de categoría y stock y se muestra el listado normal.
2. **Given** que no hay criterios activos, **When** el usuario abre el listado de productos, **Then** se conserva el contenido, orden y comportamiento actuales del listado.

### Edge Cases

- Una búsqueda vacía o compuesta únicamente por espacios no debe restringir los resultados.
- Si una categoría o un estado de stock no tiene productos coincidentes, debe mostrarse el mensaje de ausencia de resultados sin ocultar la opción de limpiar los filtros.
- Limpiar filtros desde un resultado vacío debe restaurar el listado normal.
- La búsqueda debe funcionar cuando el fragmento coincide con el nombre o con el código, incluidos los casos en que coincide con ambos.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: El usuario MUST poder buscar productos mediante un fragmento del nombre o del código.
- **FR-002**: La búsqueda MUST ignorar las diferencias entre mayúsculas y minúsculas.
- **FR-003**: El usuario MUST poder filtrar los productos por una categoría disponible.
- **FR-004**: El usuario MUST poder filtrar productos con stock y productos sin stock.
- **FR-005**: Cuando haya más de un criterio activo, el listado MUST mostrar solo productos que cumplan cada criterio activo.
- **FR-006**: Los resultados MUST aparecer en el listado de productos existente.
- **FR-007**: Si no hay productos que coincidan con los criterios activos, el listado MUST mostrar un mensaje claro y una acción para limpiar los filtros.
- **FR-008**: La acción para limpiar filtros MUST borrar el texto de búsqueda y restablecer los filtros de categoría y stock, restaurando el listado normal.
- **FR-009**: Cuando no haya filtros activos, el listado MUST conservar el contenido, orden y comportamiento que tiene actualmente.

### Key Entities *(include if feature involves data)*

- **Producto**: Elemento del catálogo con nombre, código, categoría y cantidad de stock, usado para presentar resultados.
- **Categoría**: Clasificación existente de productos, seleccionable como criterio de filtro.
- **Estado de stock**: Condición de un producto respecto a la disponibilidad de unidades: con stock o sin stock.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: En un conjunto de prueba, el 100% de las búsquedas por fragmento de nombre o código devuelve todos y solo los productos coincidentes, tanto con mayúsculas como con minúsculas.
- **SC-002**: En un conjunto de prueba, el 100% de los resultados con filtros activos cumple la categoría y el estado de stock seleccionados, además de la búsqueda textual si existe.
- **SC-003**: En todos los casos de prueba sin coincidencias, el usuario ve un mensaje de resultados vacíos y puede limpiar los filtros desde el mismo listado.
- **SC-004**: En todos los casos de prueba sin filtros, el listado conserva el mismo contenido, orden y comportamiento que antes de esta funcionalidad.
- **SC-005**: El usuario puede restablecer todos los criterios con una sola acción y volver al listado normal.

## Assumptions

- “Con stock” significa que la cantidad disponible del producto es mayor que cero; “sin stock” significa que es cero.
- Cuando se combinan criterios, estos se aplican conjuntamente: un producto debe cumplirlos todos para aparecer.
- La lista de categorías ofrecida corresponde a las categorías ya disponibles en el módulo de productos.
- Limpiar filtros restablece el listado existente sin cambiar sus opciones actuales de orden o navegación.
