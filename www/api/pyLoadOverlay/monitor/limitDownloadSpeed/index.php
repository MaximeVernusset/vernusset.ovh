<?php
require_once __DIR__.'/../monitor.php';

define('SPEED_LIMIT', 'speedLimit');

if (isConnected() && hasAuthorities([PYLOAD])) {
	if (isset($_POST[SPEED_LIMIT]) && (is_numeric($_POST[SPEED_LIMIT]) || empty($_POST[SPEED_LIMIT]))) {
		limitSpeed(intval($_POST[SPEED_LIMIT]));
		http_response_code(HTTP_OK);
		$response[MESSAGE] = 'Speed limit set';
		$response[DATA][SPEED_LIMIT] = $_POST[SPEED_LIMIT];
	} else {
		http_response_code(HTTP_BAD_REQUEST);
		$response[MESSAGE] = 'Bad request';
	}
}

saveSessionAndReturnResponse($response);