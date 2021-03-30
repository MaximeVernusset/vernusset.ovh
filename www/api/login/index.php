<?php
require_once __DIR__.'/../api.php';

if (isset($_POST[USER]) && isset($_POST[PASSWORD]) && authenticate(htmlentities($_POST[USER]), htmlentities($_POST[PASSWORD]))) {
	http_response_code(HTTP_OK);
	$response[MESSAGE] = 'logged in';
	$response[DATA][USER] = $_SESSION[USER];
	$response[DATA][AUTHORITIES] = $_SESSION[AUTHORITIES];
	$response[DATA][STAY_CONNECTED] = $_SESSION[STAY_CONNECTED];
} else {
	$response[MESSAGE] = 'failed to log in';
	http_response_code(HTTP_FORBIDDEN);
}

echo json_encode($response);

function authenticate($user, $password) {
	$users = loadUsers();
	if (isset($users[$user]) && $users[$user][HASHED_PASSWORD] == hash(HASH_ALGO, $password)) {
		$_SESSION[USER] = $user;
		$_SESSION[IS_CONNECTED] = true;
		$_SESSION[STAY_CONNECTED] = isset($_POST[STAY_CONNECTED]) && filter_var($_POST[STAY_CONNECTED], FILTER_VALIDATE_BOOLEAN);
		$_SESSION[AUTHORITIES] = $users[$user][AUTHORITIES];
		$_SESSION[LAST_REQUEST] = time();
		return true;
	}
	return false;
}