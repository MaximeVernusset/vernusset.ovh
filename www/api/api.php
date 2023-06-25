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

function httpRequest($method, $url, $headers = [], $data = [], $responseHeadersCallback = null) {
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => $method,
		CURLOPT_HTTPHEADER => $headers,
		CURLOPT_POSTFIELDS => http_build_query($data)
	));
	if (null != $responseHeadersCallback) {
		curl_setopt($curl, CURLOPT_HEADERFUNCTION, $responseHeadersCallback);
	}

	$response = curl_exec($curl);
	curl_close($curl);
	return $response;
}

function httpPost($url, $headers = [], $data = [], $responseHeadersCallback = null) {
	return httpRequest(HTTP_POST, $url, $headers, $data, $responseHeadersCallback);
}

function httpGet($url, $headers = [], $data = [], $responseHeadersCallback = null) {
	return httpRequest(HTTP_GET, $url, $headers, $data, $responseHeadersCallback);
}

function saveSessionAndReturnResponse($response) {
	if (intdiv(http_response_code(), 100) == 2) {
		customSession_save();
	}
	echo json_encode($response);
	exit();
}