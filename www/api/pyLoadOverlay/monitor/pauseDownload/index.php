<?php
require_once __DIR__.'/../monitor.php';

if (isConnected() && !sessionTimedOut() && hasAuthorities([PYLOAD])) {
	http_response_code(HTTP_OK);
	if (pauseDownload()) {
		$response[MESSAGE] = 'download paused';
	} else {
		$response[MESSAGE] = 'still downloading';
	}
}

echo json_encode($response);