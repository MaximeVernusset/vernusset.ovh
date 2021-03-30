<?php
require_once __DIR__.'/../monitor.php';

if (isConnected() && hasAuthorities([PYLOAD])) {
	http_response_code(HTTP_OK);
	$response[MESSAGE] = 'queue data';
	$response[DATA]['queue'] = getQueueData();
}

echo json_encode($response);