<?php
checkIsConnected();
checkSessionValidity();

$title = 'Collector';
$view = VIEW_DIR.'pyLoadOverlay/collector.v.php';
if (!hasAuthorities([PYLOAD])) {
	httpCode(HTTP_FORBIDDEN, $title, $view);
}

$links = array();
if (isset($_GET['links'])) {
	if ($linksTmp = json_decode($_GET['links'])) {
		$links = $linksTmp;
	} else {
		httpCode(HTTP_BAD_REQUEST, $title, $view);
	}
}

require_once VIEW_DIR.'page.v.php';

deconnectIfNeeded();