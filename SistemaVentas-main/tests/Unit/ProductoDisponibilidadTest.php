<?php

use PHPUnit\Framework\TestCase;

final class ProductoDisponibilidadTest extends TestCase
{
    private Producto $productos;

    protected function setUp(): void
    {
        $this->productos = new Producto(ProductoTestDatabase::crear());
    }

    public function testFiltraPorCategoria(): void
    {
        $resultado = $this->productos->listar(['categoria' => 1]);

        self::assertSame([1, 2], array_column($resultado, 'id_producto'));
    }

    public function testFiltraProductosConStock(): void
    {
        $resultado = $this->productos->listar(['disponibilidad' => 'con_stock']);

        self::assertSame([1, 3], array_column($resultado, 'id_producto'));
    }

    public function testFiltraProductosSinStock(): void
    {
        $resultado = $this->productos->listar(['disponibilidad' => 'sin_stock']);

        self::assertSame([2, 4], array_column($resultado, 'id_producto'));
    }

    public function testCombinaTextoCategoriaDisponibilidadEstadoYStockBajo(): void
    {
        $resultado = $this->productos->listar([
            'q' => 'Berry',
            'categoria' => 1,
            'disponibilidad' => 'sin_stock',
            'estado' => '1',
            'stock_bajo' => true,
        ]);

        self::assertSame([2], array_column($resultado, 'id_producto'));
    }

    public function testIgnoraUnaDisponibilidadNoReconocida(): void
    {
        $resultado = $this->productos->listar(['disponibilidad' => 'agotado']);

        self::assertSame([1, 2, 4, 3], array_column($resultado, 'id_producto'));
    }

    public function testDevuelveUnaListaVaciaCuandoNingunProductoCoincide(): void
    {
        $resultado = $this->productos->listar([
            'categoria' => 1,
            'disponibilidad' => 'con_stock',
            'q' => 'inexistente',
        ]);

        self::assertSame([], $resultado);
    }
}
