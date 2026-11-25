<?php
// Unidades de medida: [etiqueta, permite cantidades decimales].
function unidadesMedida(): array {
    return [
        'unidad' => ['Unidad', false], 'paquete' => ['Paquete', false], 'caja' => ['Caja', false],
        'kg' => ['Kilogramo (kg)', true], 'g' => ['Gramo (g)', true],
        'litro' => ['Litro (L)', true], 'ml' => ['Mililitro (ml)', true], 'metro' => ['Metro (m)', true],
    ];
}
function permiteDecimales(string $unidad): bool { return unidadesMedida()[$unidad][1] ?? false; }

function normalizarFiltrosListadoProductos(array $entrada, array $idsCategoriasActivas): array {
    $qEntrada = $entrada['q'] ?? '';
    $q = is_string($qEntrada) ? mb_substr(trim($qEntrada), 0, 100) : '';

    $idsCategoriasActivas = array_map(static fn($id): int => (int)$id, $idsCategoriasActivas);
    $categoria = 0;
    $categoriaEntrada = $entrada['categoria'] ?? '';
    if (is_string($categoriaEntrada) && $categoriaEntrada !== '') {
        $idCategoria = filter_var($categoriaEntrada, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($idCategoria !== false && in_array($idCategoria, $idsCategoriasActivas, true)) {
            $categoria = $idCategoria;
        }
    }

    $estadoEntrada = $entrada['estado'] ?? '1';
    $estado = is_string($estadoEntrada) && in_array($estadoEntrada, ['1', '0', ''], true)
        ? $estadoEntrada
        : '1';
    $stockBajo = ($entrada['stock_bajo'] ?? null) === '1';
    $disponibilidadEntrada = $entrada['disponibilidad'] ?? '';
    $disponibilidad = is_string($disponibilidadEntrada)
        && in_array($disponibilidadEntrada, ['con_stock', 'sin_stock'], true)
        ? $disponibilidadEntrada
        : '';

    return [
        'q' => $q,
        'categoria' => $categoria,
        'estado' => $estado,
        'stock_bajo' => $stockBajo,
        'disponibilidad' => $disponibilidad,
    ];
}

function mapearProductosCsv(array $productos, bool $verCostos): array {
    $encabezados = ['Código', 'Nombre', 'Categoría'];
    if ($verCostos) $encabezados[] = 'Costo';
    array_push($encabezados, 'Precio', 'Stock', 'Estado');

    $filas = [];
    foreach ($productos as $producto) {
        $fila = [
            (string)($producto['codigo'] ?? ''),
            (string)($producto['nombre'] ?? ''),
            (string)($producto['categoria'] ?? ''),
        ];
        if ($verCostos) {
            $fila[] = number_format((float)($producto['precio_compra'] ?? 0), 2, ',', '');
        }
        $fila[] = number_format((float)($producto['precio_venta'] ?? 0), 2, ',', '');
        $fila[] = str_replace('.', ',', (string)($producto['stock'] ?? ''));
        $fila[] = (int)($producto['estado'] ?? 0) === 1 ? 'Activo' : 'Inactivo';
        $filas[] = $fila;
    }

    return ['encabezados' => $encabezados, 'filas' => $filas];
}

function nombreArchivoProductosCsv(?DateTimeImmutable $instante = null): string {
    $zona = new DateTimeZone(date_default_timezone_get());
    $fecha = ($instante ?? new DateTimeImmutable('now', $zona))->setTimezone($zona);
    return 'productos_' . $fecha->format('Y-m-d') . '.csv';
}

function urlImagenProducto(?string $imagen): ?string {
    return $imagen ? APP_URL . '/assets/img/productos/' . rawurlencode($imagen) : null;
}
