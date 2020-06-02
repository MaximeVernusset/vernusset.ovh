<?php
require_once __DIR__.'/../api.php';

define('PYLOAD_CONFIG','pyLoadConfig');
define('PYLOAD_SESSION','pyLoadSession');
define('SPLIT_REGEX', '/(\r\n)|\r|\n/');
define('URL', 'url');
define('USERNAME', 'username');

function loadPyLoadConfig() {
	$config = null;
	if (isset($_SESSION[PYLOAD_CONFIG])) {
		$config = $_SESSION[PYLOAD_CONFIG];
	} else {
		$config = json_decode(file_get_contents(CONFIG_DIR.'pyload.json'), true);
		$_SESSION[PYLOAD_CONFIG] = $config;
	}
	return $config;
}

function getPyLoadConfig($name) {
	$config = loadPyLoadConfig();
	return isset($config[$name]) ? $config[$name] : null;
}

function loginPyLoad() {
	$sessionId = httpPost(getPyLoadConfig(URL).'/api/login', array(
		USERNAME => getPyLoadConfig(USERNAME),
		PASSWORD => getPyLoadConfig(PASSWORD)
	));
	$_SESSION[PYLOAD_SESSION] = json_decode($sessionId);
	return $sessionId !== 'false';
}

function postDownloadLinks($links) {
	if (loginPyLoad()) {
		return httpPost(getPyLoadConfig(URL).'/api/generateAndAddPackages', array(
			LINKS => json_encode($links, JSON_UNESCAPED_SLASHES),
			'session' => $_SESSION[PYLOAD_SESSION]
		));
	} else {
		http_response_code(HTTP_INTERNAL_ERROR);
		return false;
	}
}