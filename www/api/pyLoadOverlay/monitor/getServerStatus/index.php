<?php
require_once __DIR__.'/../monitor.php';

if (isConnected() && hasAuthorities([PYLOAD])) {
	http_response_code(HTTP_OK);
	$response[MESSAGE] = 'Server status';
	$response[DATA]['serverStatus'] = getServerStatus();
}

saveSessionAndReturnResponse($response);