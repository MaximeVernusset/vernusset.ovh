<?php
require_once __DIR__.'/../monitor.php';

if (isConnected() && hasAuthorities([PYLOAD])) {
	http_response_code(HTTP_OK);
	if (pauseDownload()) {
		$response[MESSAGE] = 'Download paused';
	} else {
		$response[MESSAGE] = 'Still downloading';
	}
}

saveSessionAndReturnResponse($response);