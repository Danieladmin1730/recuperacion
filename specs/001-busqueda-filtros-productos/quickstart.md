# Validación rápida: Búsqueda y filtros de productos

**Fecha**: 2026-11-24  
**Especificación**: [spec.md](./spec.md)  
**Contrato de interfaz**: [contracts/product-listing.md](./contracts/product-listing.md)

## Prerrequisitos

- PHP CLI 8.1 o superior.
- Composer disponible en la terminal.
- Extensiones `pdo_sqlite` y las requeridas por PHPUnit.
- Para la comprobación visual en la aplicación, la base MySQL del proyecto debe estar configurada y disponible.

Las pruebas automatizadas usan una base SQLite en memoria y no requieren una conexión a MySQL.
En Windows, Composer también requiere `ext-zip` habilitada o una herramienta de extracción compatible, como `unzip` o 7-Zip.

## Preparar y ejecutar PHPUnit

Desde PowerShell, en la carpeta de la aplicación:

```powershell
Set-Location SistemaVentas-main
composer install
.\vendor\bin\phpunit.bat --configuration phpunit.xml
```

Resultado esperado: PHPUnit descubre y ejecuta la suite del proyecto; todas las pruebas de búsqueda y filtros pasan.

## Resultado de validación

- PHPUnit 10.5.67, PHP 8.2.12: 10 pruebas y 10 aserciones pasaron.
- La conexión a `sistema_ventas` está disponible y el servidor embebido responde en `http://localhost:8000`.
- El listado está en `http://localhost:8000/views/productos/index.php`; sin sesión redirige al login. La comprobación visual de filtros y resultados debe completarse después de iniciar sesión con permiso `productos.ver`.

## Escenarios de validación automatizada

1. Buscar fragmentos de nombre y código con mayúsculas/minúsculas distintas; comprobar que aparecen todas y solo las coincidencias.
2. Comprobar que la búsqueda parcial existente por código de barras sigue funcionando.
3. Filtrar por categoría, con stock y sin stock; verificar que cada grupo contiene únicamente productos que satisfacen el criterio.
4. Combinar texto, categoría, disponibilidad, estado activo/inactivo y stock bajo; comprobar que se respetan todos los criterios.
5. Solicitar criterios sin coincidencias; comprobar que el resultado está vacío y que la vista ofrece limpiar filtros.
6. Ejecutar el modelo sin filtros nuevos; comparar contenido y orden con el comportamiento actual del listado.

## Comprobación visual en la aplicación

1. Iniciar la aplicación con la base y el servidor web configurados según el proyecto.
2. Iniciar sesión con una cuenta que tenga permiso para ver productos y abrir el listado existente.
3. Aplicar los criterios con el botón “Filtrar” y verificar que los resultados aparecen en la misma tabla.
4. Probar una combinación sin coincidencias; verificar el mensaje y la acción para limpiar.
5. Limpiar los filtros y confirmar que vuelve el listado predeterminado actual, incluidos sus controles y orden.
