<?php

use PHPUnit\Framework\TestCase;

final class ProductoListadoPredeterminadoTest extends TestCase
{
    public function testConservaElListadoActivoOrdenadoSinCriteriosNuevos(): void
    {
        $productos = new Producto(ProductoTestDatabase::crear());

        $resultado = $productos->listar([
            'q' => '',
            'categoria' => 0,
            'estado' => '1',
            'stock_bajo' => false,
        ]);

        self::assertSame([1, 2, 4], array_column($resultado, 'id_producto'));
    }
}
