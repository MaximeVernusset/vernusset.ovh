<?php
require_once __DIR__.'/util.php';

session_start();
http_response_code(HTTP_OK);

$controller = CONTROLLER_DIR.'home.c.php';

if (isset($_GET[ACTION])) {
	switch($_GET[ACTION]) {
	case 'login':
		$controller = CONTROLLER_DIR.'login.c.php';
		break;
	case 'logout':
		$controller = CONTROLLER_DIR.'logout.c.php';
		break;
	case 'pyload/collect':
		$controller = CONTROLLER_DIR.'pyLoadOverlay/collector.c.php';
		break;
	case 'pyload/monitor':
		$controller = CONTROLLER_DIR.'pyLoadOverlay/monitor.c.php';
		break;
	default:
		$controller = CONTROLLER_DIR.'notFound.c.php';
		break;
	}
}

require_once $controller;