# Requisitos Checklist: Exportación CSV de productos

**Purpose**: Evaluar la claridad, consistencia y cobertura de los requisitos de exportación antes de implementación
**Created**: 2026-11-24
**Feature**: [spec.md](../spec.md)

**Note**: Esta checklist evalúa la calidad de los requisitos escritos; no verifica la implementación.
**Review Ownership**: Esta checklist es un artefacto de revisión de requisitos. Solo el revisor debe marcar un ítem `[x]` después de evaluar el requisito correspondiente.
**Marker Semantics**: `[x]` significa que el criterio de calidad de requisitos fue revisado y satisfecho; no significa que la funcionalidad esté implementada.

## Requirement Completeness

- [x] CHK001 ¿Están enumerados todos los filtros que determinan las filas exportadas, incluida la búsqueda existente por código de barras? [Coverage, Spec §FR-003, Edge Cases]
- [x] CHK002 ¿Se define con suficiente precisión qué significa exportar el listado predeterminado, incluidos sus valores iniciales y límites? [Gap, Spec §FR-004, Assumptions]
- [x] CHK003 ¿Se especifica dónde y cómo se informa que no hay coincidencias, además de conservar los filtros? [Completeness, Spec §FR-008, User Story 2]
- [x] CHK004 ¿Los requisitos cubren cómo tratar los valores de texto que podrían interpretarse como fórmulas al abrir el CSV en una hoja de cálculo? [Security, Gap, Spec §FR-007, Edge Cases]

## Requirement Clarity

- [x] CHK005 ¿Quedan claros los encabezados, el orden y las columnas permitidas para cada combinación de permisos? [Clarity, Spec §FR-005–FR-006, FR-010]
- [x] CHK006 ¿Está definido qué fecha y zona horaria determinan la fecha incluida en el nombre del archivo? [Ambiguity, Spec §FR-007, Assumptions]
- [x] CHK007 ¿La compatibilidad con Excel especifica suficientemente el separador y el tratamiento de comillas, saltos de línea y caracteres acentuados? [Clarity, Spec §FR-007, Edge Cases]
- [x] CHK008 ¿Se indica la precisión y representación de costo, precio y stock en el CSV, especialmente para cantidades fraccionarias? [Gap, Spec §FR-005, Key Entities]

## Ambiguities & Conflicts

- [x] CHK009 ¿Se deja claro que la regla aclarada sobre el permiso `productos.editar` prevalece sobre la descripción inicial que enumera el costo sin restricción? [Consistency, Spec §Input, Clarifications, FR-010]

## Requirement Consistency

- [x] CHK010 ¿Son coherentes los campos y el número de columnas entre FR-005, FR-006 y SC-002 para usuarios con y sin permiso de edición? [Consistency, Spec §FR-005–FR-006, SC-002]
- [x] CHK011 ¿La promesa de exportar exactamente los resultados del listado es consistente con los requisitos de valores predeterminados y filtros sin modificar? [Consistency, Spec §FR-003–FR-004, FR-009, SC-001]

## Acceptance Criteria Quality

- [x] CHK012 ¿SC-001 define un conjunto de combinaciones de filtros evaluable, en vez de dejar abierto qué significa “combinaciones verificadas”? [Measurability, Ambiguity, Spec §SC-001]
- [x] CHK013 ¿Los criterios de éxito miden explícitamente la autorización diferencial para consultar productos y obtener el costo? [Coverage, Spec §FR-002, FR-010, SC-002]

## Scenario Coverage

- [x] CHK014 ¿Los escenarios cubren exportación normal, valores predeterminados, falta de permiso, acceso al costo según rol y ausencia de resultados? [Coverage, Spec §User Story 1, User Story 2]
- [x] CHK015 ¿Se define el comportamiento esperado al combinar búsqueda, categoría, estado, disponibilidad y stock bajo, incluidos filtros inválidos? [Coverage, Gap, Spec §FR-003, Edge Cases]

## Non-Functional Requirements

- [x] CHK016 ¿El alcance de volumen y rendimiento queda explícito o se vincula claramente con el tamaño máximo del listado existente? [Gap, Spec §SC-001, Assumptions]

## Dependencies & Assumptions

- [x] CHK017 ¿Se documenta qué comportamiento existente del listado —incluidos búsqueda, orden y valores iniciales— se considera fuente de verdad para la exportación? [Dependency, Spec §FR-003–FR-004, Assumptions]

## Notes

- Profundidad: estándar; audiencia: revisor de PR; momento: antes de implementación.
- Enfoque: consistencia de filtros y resultados, autorización por rol y calidad del CSV.
- Los 17 ítems se evaluaron contra `spec.md`; las deficiencias se corrigieron y todos los criterios de calidad quedaron satisfechos.
