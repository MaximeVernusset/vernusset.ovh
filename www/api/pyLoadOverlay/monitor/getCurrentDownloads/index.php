<?php
require_once __DIR__.'/../monitor.php';

if (isConnected() && hasAuthorities([PYLOAD])) {
	http_response_code(HTTP_OK);
	$response[MESSAGE] = 'Current downloads';
	$response[DATA]['currentDownloads'] = getCurrentDownloads();
}

saveSessionAndReturnResponse($response);