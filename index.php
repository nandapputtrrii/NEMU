<?php
declare(strict_types=1);

require_once __DIR__ . '/app/Models/BarangModel.php';
require_once __DIR__ . '/app/Controllers/HomeController.php';

$controller = new HomeController(new BarangModel(__DIR__ . '/db.json'));
$controller->index();
