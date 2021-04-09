<?php
require_once __DIR__.'/../monitor.php';

define('MAX_PARALLEL_DOWNLOADS', 'maxParallelDownloads');

if (isConnected() && hasAuthorities([PYLOAD])) {
	if (isset($_POST[MAX_PARALLEL_DOWNLOADS]) && is_numeric($_POST[MAX_PARALLEL_DOWNLOADS])) {
		setMaxParallelDownloads(intval($_POST[MAX_PARALLEL_DOWNLOADS]));
		http_response_code(HTTP_OK);
		$response[MESSAGE] = 'max parallel downloads set';
		$response[DATA][MAX_PARALLEL_DOWNLOADS] = $_POST[MAX_PARALLEL_DOWNLOADS];
	} else {
		http_response_code(HTTP_BAD_REQUEST);
		$response[MESSAGE] = 'bad request';
	}
}

saveSessionAndReturnResponse($response);