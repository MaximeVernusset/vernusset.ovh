<?php
checkIsConnected();

$title = 'Debug';
$view = 'admin/debug/variables.v.php';
if (!hasAuthorities([DEBUG])) {
	httpCode(HTTP_FORBIDDEN, $title, $view);
}
