<?php
require_once __DIR__.'/../monitor.php';

session_start();
http_response_code(HTTP_FORBIDDEN);

if (isConnected() && !sessionTimedOut() && hasAuthorities([PYLOAD])) {
	http_response_code(HTTP_OK);
	if (startDownload()) {
		$response[MESSAGE] = 'downloading';
	} else {
		$response[MESSAGE] = 'not downloading';
	}
}

echo json_encode($response);