<?php
require_once __DIR__.'/../api.php';

define('PYLOAD_SESSION','pyLoadSession');
define('SESSION','session');
define('SPLIT_REGEX', '/(\r\n)|\r|\n/');
define('USERNAME', 'username');

function getPyLoadConfig($name) {
	return getConfig($name, PYLOAD_CONFIG_FILE);
}

function loginPyLoad() {
	$sessionId = httpPost(getPyLoadConfig(URL).'/api/login', array(
		USERNAME => getPyLoadConfig(USERNAME),
		PASSWORD => getPyLoadConfig(PASSWORD)
	));
	$_SESSION[PYLOAD_SESSION] = json_decode($sessionId);
	return !filter_var($sessionId, FILTER_VALIDATE_BOOLEAN);
}

function postDownloadLinks($links) {
	if (loginPyLoad()) {
		return httpPost(getPyLoadConfig(URL).'/api/generateAndAddPackages', array(
			LINKS => json_encode($links, JSON_UNESCAPED_SLASHES),
			SESSION => $_SESSION[PYLOAD_SESSION]
		));
	} else {
		http_response_code(HTTP_INTERNAL_ERROR);
		return false;
	}
}