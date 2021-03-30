<?php
require_once __DIR__.'/../monitor.php';

define('FILE_ID', 'fileId');

if (isConnected() && hasAuthorities([PYLOAD])) {
	if (isset($_POST[FILE_ID])) {
		deleteFile(htmlentities($_POST[FILE_ID]));
		http_response_code(HTTP_OK);
		$response[MESSAGE] = 'file deleted';
		$response[DATA][FILE_ID] = $_POST[FILE_ID];
	} else {
		http_response_code(HTTP_BAD_REQUEST);
		$response[MESSAGE] = 'bad request';
	}
}

echo json_encode($response);