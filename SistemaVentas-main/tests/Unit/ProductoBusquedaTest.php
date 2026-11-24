<?php

use PHPUnit\Framework\TestCase;

final class ProductoBusquedaTest extends TestCase
{
    private Producto $productos;

    protected function setUp(): void
    {
        $this->productos = new Producto(ProductoTestDatabase::crear());
    }

    public function testBuscaFragmentoDeNombreSinDistinguirMayusculas(): void
    {
        $resultado = $this->productos->listar(['q' => 'ALMOND']);

        self::assertSame([1], array_column($resultado, 'id_producto'));
    }

    public function testBuscaFragmentoDeCodigo(): void
    {
        $resultado = $this->productos->listar(['q' => 't-20']);

        self::assertSame([2], array_column($resultado, 'id_producto'));
    }

    public function testConservaLaBusquedaPorCodigoDeBarras(): void
    {
        $resultado = $this->productos->listar(['q' => '9123']);

        self::assertSame([3], array_column($resultado, 'id_producto'));
    }
}
