<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Controllers/PagesController.php';

$controller = new PagesController();
$controller->show('lapor-hilang');
