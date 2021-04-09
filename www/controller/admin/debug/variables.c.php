<?php
checkIsConnected();

$title = 'Debug';
$view = 'admin/debug/variables.v.php';
if (!hasAuthorities([DEBUG])) {
	httpCode(HTTP_FORBIDDEN, $title, $view);
} else {
	$server = formatJson($_SERVER);
	$cookies = formatJson($_COOKIE);
	$ini = formatJson(ini_get_all());
	$session = formatJson($GLOBALS[CUSTOM_SESSION]);
}

function formatJson($var) {
	return htmlentities(json_encode($var, JSON_PRETTY_PRINT));
}