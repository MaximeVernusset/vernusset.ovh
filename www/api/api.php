<?php
require_once __DIR__.'/../util.php';

define('DATA', 'data');
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
	return httpRequest('POST', $url, $data);
}

function httpGet($url, $data = []) {
	return httpRequest('GET', $url, $data);
}