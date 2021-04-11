<?php
require_once __DIR__.'/../api.php';

if (isset($_POST[USER]) && isset($_POST[PASSWORD]) && authenticate(htmlentities($_POST[USER]), htmlentities($_POST[PASSWORD]))) {
	http_response_code(HTTP_OK);
	$response[MESSAGE] = 'Logged in';
	$response[DATA][USER] = $GLOBALS[CUSTOM_SESSION][USER];
	$response[DATA][AUTHORITIES] = $GLOBALS[CUSTOM_SESSION][AUTHORITIES];
	$response[DATA][STAY_CONNECTED] = $GLOBALS[CUSTOM_SESSION][STAY_CONNECTED];
} else {
	$response[MESSAGE] = 'Failed to log in';
	http_response_code(HTTP_FORBIDDEN);
}

saveSessionAndReturnResponse($response);

function authenticate($user, $password) {
	$users = loadUsers();
	if (isset($users[$user]) && $users[$user][HASHED_PASSWORD] == hash(HASH_ALGO, $password)) {
		$GLOBALS[CUSTOM_SESSION][USER] = $user;
		$GLOBALS[CUSTOM_SESSION][IS_CONNECTED] = true;
		$GLOBALS[CUSTOM_SESSION][STAY_CONNECTED] = isset($_POST[STAY_CONNECTED]) && filter_var($_POST[STAY_CONNECTED], FILTER_VALIDATE_BOOLEAN);
		$GLOBALS[CUSTOM_SESSION][AUTHORITIES] = $users[$user][AUTHORITIES];
		$GLOBALS[CUSTOM_SESSION][LAST_REQUEST] = time();
		return true;
	}
	return false;
}