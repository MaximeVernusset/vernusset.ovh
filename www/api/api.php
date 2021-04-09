<?php
require_once __DIR__.'/../util.php';

define('DATA', 'data');
define('HTTP_GET', 'GET');
define('HTTP_POST', 'POST');
define('MESSAGE', 'message');

header('Content-Type: application/json');
$response = array(
	MESSAGE => '',
	DATA => []
);

function httpRequest($method, $url, $data = []) {
	$options = array(
		'http' => array(
			'header'  => 'Content-type: application/x-www-form-urlencoded\r\n',
			'method'  => $method,
			'content' => http_build_query($data)
		)
	);
	$context = stream_context_create($options);
	return file_get_contents($url, false, $context);
}

function httpPost($url, $data = []) {
	return httpRequest(HTTP_POST, $url, $data);
}

function httpGet($url, $data = []) {
	return httpRequest(HTTP_GET, $url, $data);
}

function saveSessionAndReturnResponse($response) {
	if (intdiv(http_response_code(), 100) == 2) {
		customSession_save();
	}
	echo json_encode($response);
	exit();
}