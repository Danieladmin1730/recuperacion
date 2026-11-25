<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/permisos.php';
require_once __DIR__ . '/../controllers/ProductoController.php';

requirePermission('productos.ver');

(new ProductoController())->exportarListado($_GET);
