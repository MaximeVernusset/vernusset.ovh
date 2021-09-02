<?php
require_once __DIR__.'/util.php';

http_response_code(HTTP_OK);

$controller = 'home.c.php';

if (isset($_GET[ACTION])) {
	switch($_GET[ACTION]) {
	case 'login':
		$controller = 'login.c.php';
		break;
	case 'pyload/collect':
		$controller = 'pyLoadOverlay/collector.c.php';
		break;
	case 'pyload/monitor':
		$controller = 'pyLoadOverlay/monitor.c.php';
		break;	
	case 'cam/video':
		$controller = 'camera/video.c.php';
		break;
	case 'admin/debug/var':
		$controller = 'admin/debug/variables.c.php';
		break;
	default:
		$controller = 'notFound.c.php';
		break;
	}
}

require_once CONTROLLER_DIR.$controller;
require_once VIEW_DIR.'page.v.php';

customSession_save();