<?php
require_once __DIR__.'/../monitor.php';

if (isConnected() && !sessionTimedOut() && hasAuthorities([PYLOAD])) {
	http_response_code(HTTP_OK);
	$response[MESSAGE] = 'current downloads';
	$response[DATA]['currentDownloads'] = getCurrentDownloads();
}

echo json_encode($response);