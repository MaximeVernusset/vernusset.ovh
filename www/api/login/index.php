<?php
require_once __DIR__.'/../../util.php';

session_start();

if (isset($_POST[USER]) && isset($_POST[PASSWORD]) && authenticate($_POST[USER], $_POST[PASSWORD])) {
	http_response_code(HTTP_OK);
} else {
	http_response_code(HTTP_FORBIDDEN);
}

function authenticate($user , $password) {
	$users = loadUsers();
	if (isset($users[$user]) && $users[$user][HASHED_PASSWORD] == hash(HASH_ALGO, $password)) {
		$_SESSION[USER] = $user;
		$_SESSION[IS_CONNECTED] = true;
		$_SESSION[STAY_CONNECTED] = isset($_POST[STAY_CONNECTED]) && $_POST[STAY_CONNECTED];
		$_SESSION[AUTHORITIES] = $users[$user][AUTHORITIES];
		$_SESSION[LAST_LOGIN] = time();
		return true;
	}
	return false;
}