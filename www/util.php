<?php
define('ACTION', 'action');
define('ADMIN', 'admin');
define('AUTHORITIES', 'authorities');
define('CAMERAS', 'cameras');
define('CAMERAS_CONFIG_FILE', 'cameras.json');
define('CAMERAS_DIR', __DIR__.'/../cameras/');
define('CAMERA_URL_OVERRIDE_FILE_FORMAT', '%s-%s');
define('CONFIG', 'config');
define('CONFIG_DIR', __DIR__.'/../config/');
define('CONTROLLER_DIR', __DIR__.'/controller/');
define('CUSTOM_SESSION', 'customSession');
define('CUSTOM_SESSION_COOKIE_PATH', '/');
define('CUSTOM_SESSION_ID', 'CSID');
define('DEBUG', 'debug');
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
define('SESSIONS_DIR', __DIR__.'/../sessions/');
define('STAY_CONNECTED', 'stayConnected');
define('URL', 'url');
define('USER', 'user');
define('USERS_FILE', 'users.json');
define('VIDEO', 'video');
define('VIEW_DIR', __DIR__.'/view/');
define('VISITOR', 'visitor');

http_response_code(HTTP_NOT_FOUND);
customSession_start();

function customSession_start() {
	if (!file_exists(SESSIONS_DIR)) {
		mkdir(SESSIONS_DIR);
	}
	cleanExpiredCustomSessions();

	$customSid = bin2hex(openssl_random_pseudo_bytes(16));
	if (isset($_COOKIE[CUSTOM_SESSION_ID])) {
		$customSid = $_COOKIE[CUSTOM_SESSION_ID];
	} else {
		setcookie(CUSTOM_SESSION_ID, $customSid, time() + getConfig(SESSION_TIMEOUT) * 60, CUSTOM_SESSION_COOKIE_PATH, false, true);
	}

	$GLOBALS[CUSTOM_SESSION] = array(CUSTOM_SESSION_ID => $customSid);
	$sessionFileName = SESSIONS_DIR.$customSid;
	if (file_exists($sessionFileName)) {
		$GLOBALS[CUSTOM_SESSION] = openSessionFile($sessionFileName);
	}
}

function customSession_save() {
	$sessionFileName = SESSIONS_DIR.$GLOBALS[CUSTOM_SESSION][CUSTOM_SESSION_ID];
	file_put_contents($sessionFileName, base64_encode(json_encode($GLOBALS[CUSTOM_SESSION])));
}

function customSession_destroy() {
	unlink(SESSIONS_DIR.$GLOBALS[CUSTOM_SESSION][CUSTOM_SESSION_ID]);
	unset($GLOBALS[CUSTOM_SESSION]);
}

function openSessionFile($sessionFileName) {
	return json_decode(base64_decode(file_get_contents($sessionFileName)), true);
}

function cleanExpiredCustomSessions() {
	$rand = rand(0, 10);
	if ($rand == 0) {
		$now = time();
		$timeout = getConfig(SESSION_TIMEOUT) * 60;
		foreach (scandir(SESSIONS_DIR) as $sid) {
			$sessionFileName = SESSIONS_DIR.$sid;
			if (is_file($sessionFileName)) {
				$session = openSessionFile($sessionFileName);
				if (!isset($session[LAST_REQUEST]) || $session[LAST_REQUEST] + $timeout < $now) {
					unlink($sessionFileName);
				}
			}
		}
	}
}

function loadConfig($configFile) {
	$config = null;
	if ($configFile !== USERS_FILE) {
		$config = json_decode(file_get_contents(CONFIG_DIR.$configFile), true);
	}
	return $config;
}

function clearSessionAndCookie() {
	customSession_destroy();
	setcookie(CUSTOM_SESSION_ID, '', time() - 1, CUSTOM_SESSION_COOKIE_PATH);
}

function askToLogin() {
	header(LOCATION_HEADER.'?action=login&redirectUrl='.urlencode(REQUESTED_URL));
	clearSessionAndCookie();
	exit;
}

function renewSessionCookie($lifetime) {
	setcookie(CUSTOM_SESSION_ID, $_COOKIE[CUSTOM_SESSION_ID], time() + $lifetime, CUSTOM_SESSION_COOKIE_PATH, false, true);
}

function isConnected() {
	$now = time();
	$timedOut = false;
	$isConnected = isset($GLOBALS[CUSTOM_SESSION][IS_CONNECTED]) && $GLOBALS[CUSTOM_SESSION][IS_CONNECTED];
	if (isset($GLOBALS[CUSTOM_SESSION][STAY_CONNECTED])) {
		$timeout = getConfig(SESSION_TIMEOUT) * 60;
		if (!$GLOBALS[CUSTOM_SESSION][STAY_CONNECTED] && isset($GLOBALS[CUSTOM_SESSION][LAST_REQUEST])) {
			$timedOut = $GLOBALS[CUSTOM_SESSION][LAST_REQUEST] + $timeout < $now;
		} else if ($GLOBALS[CUSTOM_SESSION][STAY_CONNECTED]) {
			renewSessionCookie($timeout);
		}
	}
	$GLOBALS[CUSTOM_SESSION][LAST_REQUEST] = $now;
	return $isConnected && !$timedOut;
}

function checkIsConnected() {
	if (!isConnected()) {
		askToLogin();
	}
}

function getUser() {
	return isset($GLOBALS[CUSTOM_SESSION][USER]) ? $GLOBALS[CUSTOM_SESSION][USER] : VISITOR;
}

function loadUsers() {
	return json_decode(file_get_contents(CONFIG_DIR.USERS_FILE), true);
}

function getConfig($name, $configFile = GENERAL_CONFIG_FILE) {
	$config = loadConfig($configFile);
	return isset($config[$name]) ? $config[$name] : null;
}

function hasAuthorities($authorities) {
	$hasAuthorities = isset($GLOBALS[CUSTOM_SESSION][AUTHORITIES]);
	if ($hasAuthorities && !in_array(ADMIN, $GLOBALS[CUSTOM_SESSION][AUTHORITIES])) {
		foreach ($authorities as $authority) {
			$hasAuthorities &= in_array($authority, $GLOBALS[CUSTOM_SESSION][AUTHORITIES]);
		}
	}
	return $hasAuthorities;
}

function httpCode($code, &$title, &$view) {
	http_response_code($code);
	$title = $code;
	$view = sprintf(VIEW_DIR.'errors/%d.html', $code);
}