<?php
require_once __DIR__.'/../monitor.php';

if (isConnected() && hasAuthorities([PYLOAD])) {
	http_response_code(HTTP_OK);
	$response[MESSAGE] = 'Download configuration';
	$response[DATA]['downloadConfig'] = getDownloadConfig();
}

saveSessionAndReturnResponse($response);