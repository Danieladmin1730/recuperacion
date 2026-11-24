<?php

final class ProductoTestDatabase
{
    public static function crear(bool $comparacionSensible = true): PDO
    {
        $db = new PDO('sqlite::memory:');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->exec('PRAGMA case_sensitive_like = ' . ($comparacionSensible ? 'ON' : 'OFF'));
        $db->exec('CREATE TABLE categorias (
            id_categoria INTEGER PRIMARY KEY,
            nombre TEXT NOT NULL,
            estado INTEGER NOT NULL
        )');
        $db->exec('CREATE TABLE productos (
            id_producto INTEGER PRIMARY KEY,
            id_categoria INTEGER NOT NULL,
            codigo TEXT NOT NULL,
            codigo_barras TEXT,
            nombre TEXT NOT NULL,
            stock REAL NOT NULL,
            stock_minimo REAL NOT NULL,
            estado INTEGER NOT NULL
        )');

        $insertarCategoria = $db->prepare(
            'INSERT INTO categorias (id_categoria, nombre, estado) VALUES (:id, :nombre, :estado)'
        );
        foreach ([
            [1, 'Bebidas', 1],
            [2, 'Abarrotes', 1],
        ] as [$id, $nombre, $estado]) {
            $insertarCategoria->execute(['id' => $id, 'nombre' => $nombre, 'estado' => $estado]);
        }

        $insertarProducto = $db->prepare(
            'INSERT INTO productos
                (id_producto, id_categoria, codigo, codigo_barras, nombre, stock, stock_minimo, estado)
             VALUES
                (:id, :categoria, :codigo, :codigo_barras, :nombre, :stock, :stock_minimo, :estado)'
        );
        foreach ([
            [1, 1, 'AM-101', '123-ALM', 'Almond Milk', 10, 2, 1],
            [2, 1, 'BT-202', null, 'Berry Tea', 0, 1, 1],
            [3, 2, 'CB-303', '912345', 'Cocoa Bar', 4, 5, 0],
            [4, 2, 'VB-404', null, 'Vanilla Beans', 0, 0, 1],
        ] as [$id, $categoria, $codigo, $codigoBarras, $nombre, $stock, $stockMinimo, $estado]) {
            $insertarProducto->execute([
                'id' => $id,
                'categoria' => $categoria,
                'codigo' => $codigo,
                'codigo_barras' => $codigoBarras,
                'nombre' => $nombre,
                'stock' => $stock,
                'stock_minimo' => $stockMinimo,
                'estado' => $estado,
            ]);
        }

        return $db;
    }
}
