<?php
checkIsConnected();

$title = 'Monitor';
$view = VIEW_DIR.'pyLoadOverlay/monitor.v.php';
if (!hasAuthorities([PYLOAD])) {
	httpCode(HTTP_FORBIDDEN, $title, $view);
}

require_once VIEW_DIR.'page.v.php';

deconnectIfNeeded();