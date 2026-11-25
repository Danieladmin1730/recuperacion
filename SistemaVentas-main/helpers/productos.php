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

function urlImagenProducto(?string $imagen): ?string {
    return $imagen ? APP_URL . '/assets/img/productos/' . rawurlencode($imagen) : null;
}
