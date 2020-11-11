<?php
require_once __DIR__.'/../monitor.php';

if (isConnected() && !sessionTimedOut() && hasAuthorities([PYLOAD])) {
	http_response_code(HTTP_OK);
	$response[MESSAGE] = 'download configuration';
	$response[DATA]['downloadConfig'] = getDownloadConfig();
}

echo json_encode($response);