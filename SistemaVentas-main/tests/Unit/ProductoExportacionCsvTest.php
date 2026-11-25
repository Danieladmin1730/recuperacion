<?php

use PHPUnit\Framework\TestCase;

final class ProductoExportacionCsvTest extends TestCase
{
    public function testMapeaColumnasYValoresSegunPermisoDeEdicion(): void
    {
        $productos = [[
            'codigo' => 'A-1',
            'nombre' => 'Café',
            'categoria' => 'Bebidas',
            'precio_compra' => '12.30',
            'precio_venta' => '19.99',
            'stock' => '1.250',
            'estado' => 1,
        ]];

        $conCosto = mapearProductosCsv($productos, true);
        $sinCosto = mapearProductosCsv($productos, false);

        self::assertSame(
            ['Código', 'Nombre', 'Categoría', 'Costo', 'Precio', 'Stock', 'Estado'],
            $conCosto['encabezados']
        );
        self::assertSame(
            [['A-1', 'Café', 'Bebidas', '12,30', '19,99', '1,250', 'Activo']],
            $conCosto['filas']
        );
        self::assertSame(
            ['Código', 'Nombre', 'Categoría', 'Precio', 'Stock', 'Estado'],
            $sinCosto['encabezados']
        );
        self::assertSame(
            [['A-1', 'Café', 'Bebidas', '19,99', '1,250', 'Activo']],
            $sinCosto['filas']
        );
    }

    public function testRepresentaProductosInactivosConLaEtiquetaEnEspanol(): void
    {
        $producto = [
            'codigo' => 'A-2',
            'nombre' => 'Producto',
            'categoria' => 'General',
            'precio_compra' => '0.00',
            'precio_venta' => '1.00',
            'stock' => '0.000',
            'estado' => 0,
        ];

        $resultado = mapearProductosCsv([$producto], false);

        self::assertSame('Inactivo', $resultado['filas'][0][5]);
    }

    public function testProtegeLosPrefijosDeFormulaSinAlterarOtrosValores(): void
    {
        self::assertSame(
            ["'=1+1", "'+cmd", "'-1+1", "'@SUM(A1:A2)", 'texto', 12],
            protegerFilaCsvContraFormulas(['=1+1', '+cmd', '-1+1', '@SUM(A1:A2)', 'texto', 12])
        );
    }

    public function testNombreUsaElFormatoYLaFechaDeBogotaEnUnLimiteDeDia(): void
    {
        $zonaOriginal = date_default_timezone_get();
        date_default_timezone_set('America/Bogota');

        try {
            $instanteUtc = new DateTimeImmutable('2026-11-24T01:00:00+00:00');
            $nombre = nombreArchivoProductosCsv($instanteUtc);

            self::assertSame('productos_2026-11-23.csv', $nombre);
            self::assertMatchesRegularExpression('/^productos_\d{4}-\d{2}-\d{2}\.csv$/', $nombre);
        } finally {
            date_default_timezone_set($zonaOriginal);
        }
    }
}
