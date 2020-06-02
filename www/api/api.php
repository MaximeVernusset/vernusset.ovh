<?php
require_once __DIR__.'/../util.php';

define('DATA', 'data');
define('MESSAGE', 'message');

header('Content-Type: application/json');
$response = array(
	MESSAGE => '',
	DATA => []
);

function httpPost($url, $data) {
	$options = array(
		'http' => array(
			'header'  => 'Content-type: application/x-www-form-urlencoded\r\n',
			'method'  => 'POST',
			'content' => http_build_query($data)
		)
	);
	$context = stream_context_create($options);
	return file_get_contents($url, false, $context);
}