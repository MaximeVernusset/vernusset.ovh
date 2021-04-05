<?php
checkIsConnected();

$title = 'Monitor';
$view = 'pyLoadOverlay/monitor.v.php';
if (!hasAuthorities([PYLOAD])) {
	httpCode(HTTP_FORBIDDEN, $title, $view);
}
