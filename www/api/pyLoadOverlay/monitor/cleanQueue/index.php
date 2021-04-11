<?php
require_once __DIR__.'/../monitor.php';

if (isConnected() && hasAuthorities([PYLOAD])) {
	http_response_code(HTTP_OK);
	$response[MESSAGE] = 'Queue cleaned';
	$response[DATA][PACKAGES_IDS] = cleanQueue();
}

saveSessionAndReturnResponse($response);