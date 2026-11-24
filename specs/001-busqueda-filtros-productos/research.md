# Research: Búsqueda y filtros de productos

**Fecha**: 2026-11-24  
**Especificación**: [spec.md](./spec.md)

## Hallazgos del proyecto

- `SistemaVentas-main/composer.json` declara PHP `^8.1` y no declara PHPUnit ni dependencias de desarrollo.
- El proyecto conecta a MySQL mediante PDO en `SistemaVentas-main/config/database.php`.
- `Producto::listar()` ya busca parcialmente por nombre, código y código de barras; filtra por categoría, estado activo/inactivo y stock bajo, usa parámetros preparados y limita la consulta a 500 resultados.
- La vista actual de productos envía los filtros por GET y usa el botón “Filtrar”; por defecto muestra productos activos. La salida dinámica ya usa `e()`.
- No se encontraron pruebas PHP ni configuración de PHPUnit existentes.
- El PHP CLI inspeccionado incluye PDO SQLite, de modo que las pruebas del modelo pueden ejecutarse contra una base efímera sin conectar a la base operativa.

## Decisiones

### PHPUnit compatible con la versión mínima de PHP

**Decisión**: Incorporar PHPUnit `^10.5` con Composer como dependencia de desarrollo.

**Razón**: El proyecto admite PHP 8.1; la tabla oficial de PHPUnit indica que la rama 10.5 es compatible desde PHP 8.1. PHPUnit 11.5 requiere PHP 8.2, lo que elevaría innecesariamente la versión mínima efectiva. PHPUnit 10.5 está en estado de soporte vital, pero es la rama compatible con el requisito actual del proyecto.

**Alternativas consideradas**:
- PHPUnit 11.5: no cubre el PHP 8.1 admitido por el proyecto.
- Ejecutar un PHAR independiente: no satisface el requisito de incluir PHPUnit con Composer como dependencia de desarrollo.

**Fuentes**:
- [Versiones soportadas por PHPUnit](https://phpunit.de/supported-versions.html)
- [Instalación de PHPUnit 10.5](https://docs.phpunit.de/en/10.5/installation.html)

### Pruebas aisladas del modelo

**Decisión**: Probar `Producto::listar()` con una conexión PDO SQLite en memoria y fixtures mínimos de productos y categorías.

**Razón**: El modelo recibe PDO por constructor y las pruebas solo necesitan verificar la consulta de lectura; la base efímera evita depender de MySQL, credenciales o datos operativos. PHPUnit admite organizar y descubrir las pruebas desde un directorio dedicado y mediante configuración XML.

**Alternativas consideradas**:
- Una base MySQL compartida: añade configuración externa y riesgo de afectar datos de desarrollo.
- Pruebas con un doble manual de PDO: no verifica la consulta SQL real.

**Prerequisito**: La CLI de PHP usada para ejecutar PHPUnit debe tener `pdo_sqlite` habilitado. Está habilitado en el entorno inspeccionado.

**Fuente**:
- [Organización de pruebas con PHPUnit 10.5](https://docs.phpunit.de/en/10.5/organizing-tests.html)

### Compatibilidad de datos

**Decisión**: Derivar disponibilidad del valor de stock existente, combinar filtros activos con AND y el texto con coincidencia parcial en los campos de búsqueda, sin agregar columnas ni tablas.

**Razón**: La especificación define “con stock” como una cantidad mayor que cero y “sin stock” como cero. Las entidades y relaciones ya existen; la disponibilidad es un criterio de consulta, no un dato nuevo que deba persistirse.

**Alternativas consideradas**:
- Agregar un estado persistido de disponibilidad: duplicaría información derivable de `productos.stock` y requeriría mantenerlo sincronizado.

### Interacción y compatibilidad del listado

**Decisión**: Reutilizar el formulario GET y el botón “Filtrar” actuales. Añadir disponibilidad como filtro separado, manteniendo categoría, estado activo/inactivo, stock bajo y búsqueda actual por código de barras.

**Razón**: Conserva el flujo y las capacidades existentes. La limpieza vuelve al listado predeterminado actual (productos activos, sin otros criterios), y no altera el orden ni el límite existentes.

**Alternativas consideradas**:
- Reemplazar el estado activo/inactivo por disponibilidad: contradice la aclaración de la especificación y elimina un filtro existente.
- Actualizar resultados en cada pulsación: modifica el patrón actual del formulario sin necesidad funcional.
