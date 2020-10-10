<?php
define('ACTION', 'action');
define('ADMIN', 'admin');
define('AUTHORITIES', 'authorities');
define('CONFIG', 'config');
define('CONFIG_DIR', __DIR__.'/../config/');
define('CONTROLLER_DIR', __DIR__.'/controller/');
define('DEFAULT_PAGE', 'index.php');
define('GENERAL_CONFIG_FILE', 'config.json');
define('HASH_ALGO', 'sha256');
define('HASHED_PASSWORD', 'hashedPassword');
define('HTTP_BAD_REQUEST', 400);
define('HTTP_FORBIDDEN', 403);
define('HTTP_INTERNAL_ERROR', 500);
define('HTTP_NOT_FOUND', 404);
define('HTTP_OK', 200);
define('IS_CONNECTED', 'isConnected');
define('LAST_LOGIN', 'lastLogin');
define('LAST_REQUEST', 'lastRequest');
define('LINKS', 'links');
define('LOCATION_HEADER', 'location: ');
define('PACKAGE_NAME', 'name');
define('PASSWORD', 'password');
define('PYLOAD', 'pyload');
define('PYLOAD_CONFIG_FILE', 'pyload.json');
define('REDIRECT_URL', 'redirectUrl');
define('REQUESTED_URL', $_SERVER['REQUEST_URI']);
define('SESSION_TIMEOUT', 'sessionTimeOut');
define('STAY_CONNECTED', 'stayConnected');
define('URL', 'url');
define('USER', 'user');
define('USERS_FILE', 'users.json');
define('VIEW_DIR', __DIR__.'/view/');
define('VISITOR', 'visitor');

ini_set('session.cookie_httponly', 1);
http_response_code(HTTP_NOT_FOUND);

function loadConfig($configFile) {
	$config = null;
	if ($configFile !== USERS_FILE) {
		if (isset($_SESSION[$configFile])) {
			$config = $_SESSION[$configFile];
		} else {
			$config = json_decode(file_get_contents(CONFIG_DIR.$configFile), true);
			$_SESSION[$configFile] = $config;
		}
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
	$now = time();
	$timedOut = true;
	$timeout = getConfig(SESSION_TIMEOUT) * 60;
	if (isset($_SESSION[LAST_LOGIN])) {
		$timedOut = $_SESSION[LAST_LOGIN] + $timeout < $now;
		if (isset($_SESSION[LAST_REQUEST])) {
			$timedOut &= $_SESSION[LAST_REQUEST] + $timeout < $now;
		}
	}
	$_SESSION[LAST_REQUEST] = $now;
	return $timedOut;
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
	return json_decode(file_get_contents(CONFIG_DIR.USERS_FILE), true);
}

function getConfig($name, $configFile = GENERAL_CONFIG_FILE) {
	$config = loadConfig($configFile);
	return isset($config[$name]) ? $config[$name] : null;
}

function hasAuthorities($authorities) {
	$hasAuthorities = isset($_SESSION[AUTHORITIES]);
	if ($hasAuthorities && !in_array(ADMIN, $_SESSION[AUTHORITIES])) {
		foreach ($authorities as $authority) {
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