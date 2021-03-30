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

$cookieLifetimeInSeconds = getConfig(SESSION_TIMEOUT) * 60 * 10;
ini_set('session.cookie_httponly', 1);
ini_set('session.gc_maxlifetime', $cookieLifetimeInSeconds);
session_set_cookie_params($cookieLifetimeInSeconds);

http_response_code(HTTP_NOT_FOUND);
session_start();

function loadConfig($configFile) {
	$config = null;
	if ($configFile !== USERS_FILE) {
		$config = json_decode(file_get_contents(CONFIG_DIR.$configFile), true);
	}
	return $config;
}

function clearSessionAndCookie() {
	session_unset();
	session_destroy();
	setcookie(session_name(), '', time() - 1, '/');
}

function askToLogin() {
	header(LOCATION_HEADER.'?action=login&redirectUrl='.urlencode(REQUESTED_URL));
	clearSessionAndCookie();
	exit;
}

function isConnected() {
	$isConnected = isset($_SESSION[IS_CONNECTED]) && $_SESSION[IS_CONNECTED];
	$now = time();
	$timedOut = true;
	$timeout = getConfig(SESSION_TIMEOUT) * 60;
	if (isset($_SESSION[LAST_REQUEST])) {
		$timedOut = $_SESSION[LAST_REQUEST] + $timeout < $now;
	}
	$_SESSION[LAST_REQUEST] = $now;
	return $isConnected && !$timedOut;
}

function checkIsConnected() {
	if (!isConnected()) {
		askToLogin();
	}
}

function getUser() {
	return isset($_SESSION[USER]) ? $_SESSION[USER] : VISITOR;
}

function deconnectIfNeeded() {
	if (isset($_SESSION[STAY_CONNECTED]) && !$_SESSION[STAY_CONNECTED]) {
		clearSessionAndCookie();
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