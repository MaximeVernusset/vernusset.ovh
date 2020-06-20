<?php
checkIsConnected();
checkSessionValidity();

$title = 'Collector';
$view = VIEW_DIR.'pyLoadOverlay/collector.v.php';
if (!hasAuthorities([PYLOAD])) {
	httpCode(HTTP_FORBIDDEN, $title, $view);
} else {
	$packageName = isset($_GET[PACKAGE_NAME]) ? htmlentities($_GET[PACKAGE_NAME]) : '';
	$links = array();
	if (isset($_GET[LINKS])) {
		if ($linksTmp = json_decode($_GET[LINKS])) {
			$links = $linksTmp;
		} else {
			httpCode(HTTP_BAD_REQUEST, $title, $view);
		}
	}
}

require_once VIEW_DIR.'page.v.php';

deconnectIfNeeded();