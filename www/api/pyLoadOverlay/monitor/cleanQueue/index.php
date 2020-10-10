<?php
require_once __DIR__.'/../monitor.php';

session_start();
http_response_code(HTTP_FORBIDDEN);

if (isConnected() && !sessionTimedOut() && hasAuthorities([PYLOAD])) {
	http_response_code(HTTP_OK);
	$response[MESSAGE] = 'queue cleaned';
	$response[DATA][PACKAGES_IDS] = cleanQueue();
}

echo json_encode($response);