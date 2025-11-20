<?php
require_once '../config/config.php';
requireAuth();

require_once '../src/controllers/DashboardController.php';

$controller = new DashboardController();
$controller->index();
?>