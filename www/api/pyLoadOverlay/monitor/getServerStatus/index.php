<?php
require_once __DIR__.'/../monitor.php';

session_start();
http_response_code(HTTP_FORBIDDEN);

if (isConnected() && !sessionTimedOut() && hasAuthorities([PYLOAD])) {
	http_response_code(HTTP_OK);
	$response[MESSAGE] = 'server status';
	$response[DATA]['status'] = getServerStatus();
}

echo json_encode($response);