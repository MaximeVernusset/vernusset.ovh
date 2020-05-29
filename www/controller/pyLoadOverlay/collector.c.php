<?php
checkIsConnected();
checkSessionValidity();

$title = 'Collector';
$view = VIEW_DIR.'pyLoadOverlay/collector.v.php';
if (!hasAuthorities([PYLOAD])) {
	httpCode(HTTP_FORBIDDEN, $title, $view);
}

require_once VIEW_DIR.'page.v.php';

deconnectIfNeeded();