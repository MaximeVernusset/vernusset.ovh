<?php
define('ACTION', 'action');
define('ADMIN', 'admin');
define('AUTHORITIES', 'authorities');
define('CONFIG','config');
define('CONFIG_DIR', __DIR__.'/../config/');
define('CONTROLLER_DIR', __DIR__.'/controller/');
define('DEFAULT_PAGE', 'index.php');
define('HASH_ALGO', 'sha256');
define('HASHED_PASSWORD', 'hashedPassword');
define('HTTP_FORBIDDEN', 403);
define('HTTP_NOT_FOUND', 404);
define('HTTP_OK', 200);
define('IS_CONNECTED', 'isConnected');
define('LAST_LOGIN', 'lastLogin');
define('LOCATION_HEADER', 'location: ');
define('PASSWORD', 'password');
define('PYLOAD', 'pyload');
define('REDIRECT_URL', 'redirectUrl');
define('REQUESTED_URL', $_SERVER['REQUEST_URI']);
define('SESSION_TIMEOUT', 'sessionTimeOut');
define('STAY_CONNECTED', 'stayConnected');
define('USER', 'user');
define('VIEW_DIR', __DIR__.'/view/');
define('VISITOR', 'visitor');

function loadConfig() {
	$config = null;
	if (isset($_SESSION[CONFIG])) {
		$config = $_SESSION[CONFIG];
	} else {
		$config = json_decode(file_get_contents(CONFIG_DIR.'config.json'), true);
		$_SESSION[CONFIG] = $config;
	}
	return $config;
}

function askToLogin() {
	header(LOCATION_HEADER.'?action=login&redirectUrl='.urlencode(REQUESTED_URL));
	session_destroy();
	exit;
}

function isConnected() {
	return isset($_SESSION[IS_CONNECTED]) && $_SESSION[IS_CONNECTED];
}

function checkIsConnected() {
	if (!isConnected()) {
		askToLogin();
	}
}

function sessionTimedOut() {
	if (isset($_SESSION[LAST_LOGIN])) {
		return $_SESSION[LAST_LOGIN] + getConfig(SESSION_TIMEOUT) * 60 < time();
	}
	return true;
}

function checkSessionValidity() {
	if (sessionTimedOut()) {
		askToLogin();
	}
}

function getUser() {
	return isset($_SESSION[USER]) ? $_SESSION[USER] : VISITOR;
}

function deconnectIfNeeded() {
	if (isset($_SESSION[STAY_CONNECTED]) && !$_SESSION[STAY_CONNECTED]) {
		session_destroy();
	}
}

function loadUsers() {
	return json_decode(file_get_contents(CONFIG_DIR.'users.json'), true);
}

function getConfig($name) {
	$config = loadConfig();
	return isset($config[$name]) ? $config[$name] : null;
}

function hasAuthorities($authorities) {
	$hasAuthorities = isset($_SESSION[AUTHORITIES]);
	if ($hasAuthorities && !in_array(ADMIN, $_SESSION[AUTHORITIES])) {
		foreach($authorities as $authority) {
			$hasAuthorities &= in_array($authority, $_SESSION[AUTHORITIES]);
		}
	}
	return $hasAuthorities;
}

function httpCode($code, &$title, &$view) {
	http_response_code($code);
	$title = $code;
	$view = sprintf(VIEW_DIR.'errors/%d.html', $code);
}