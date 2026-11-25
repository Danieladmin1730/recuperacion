<?php

use PHPUnit\Framework\TestCase;

final class ProductoFiltrosListadoTest extends TestCase
{
    public function testRecortaYLimitaLaBusquedaYDescartaValoresNoTextuales(): void
    {
        $filtros = normalizarFiltrosListadoProductos([
            'q' => '  ' . str_repeat('a', 101) . '  ',
        ], [1]);

        self::assertSame(str_repeat('a', 100), $filtros['q']);

        $filtros = normalizarFiltrosListadoProductos(['q' => ['inesperado']], [1]);

        self::assertSame('', $filtros['q']);
    }

    public function testSoloAceptaIdentificadoresPositivosDeCategoriasActivas(): void
    {
        $valido = normalizarFiltrosListadoProductos(['categoria' => '2'], [1, 2]);
        $invalido = normalizarFiltrosListadoProductos(['categoria' => '2x'], [1, 2]);
        $inactivo = normalizarFiltrosListadoProductos(['categoria' => '3'], [1, 2]);
        $noPositivo = normalizarFiltrosListadoProductos(['categoria' => '0'], [1, 2]);
        $noTextual = normalizarFiltrosListadoProductos(['categoria' => ['2']], [1, 2]);

        self::assertSame(2, $valido['categoria']);
        self::assertSame(0, $invalido['categoria']);
        self::assertSame(0, $inactivo['categoria']);
        self::assertSame(0, $noPositivo['categoria']);
        self::assertSame(0, $noTextual['categoria']);
    }

    public function testUsaValoresPredeterminadosParaEstadoYDisponibilidadInvalidos(): void
    {
        $filtros = normalizarFiltrosListadoProductos([
            'estado' => ['1'],
            'disponibilidad' => ['sin_stock'],
        ], [1]);

        self::assertSame('1', $filtros['estado']);
        self::assertSame('', $filtros['disponibilidad']);
    }

    public function testConservaLosValoresPermitidosDeEstadoYDisponibilidad(): void
    {
        $filtros = normalizarFiltrosListadoProductos([
            'estado' => '0',
            'disponibilidad' => 'sin_stock',
        ], [1]);

        self::assertSame('0', $filtros['estado']);
        self::assertSame('sin_stock', $filtros['disponibilidad']);
    }

    public function testSoloActivaStockBajoConElValorPermitido(): void
    {
        $predeterminado = normalizarFiltrosListadoProductos([], [1]);
        $valido = normalizarFiltrosListadoProductos(['stock_bajo' => '1'], [1]);
        $invalido = normalizarFiltrosListadoProductos(['stock_bajo' => 'true'], [1]);

        self::assertFalse($predeterminado['stock_bajo']);
        self::assertTrue($valido['stock_bajo']);
        self::assertFalse($invalido['stock_bajo']);
    }
}
