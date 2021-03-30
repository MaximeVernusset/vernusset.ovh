<?php
$redirectUrl = isset($_GET[REDIRECT_URL]) ? urldecode($_GET[REDIRECT_URL]) : DEFAULT_PAGE;

if (isConnected()) {
	header(LOCATION_HEADER.$redirectUrl);
	exit;
}

$title = 'Login';
$view = VIEW_DIR.'login.v.php';

require_once VIEW_DIR.'page.v.php';

function formatSessionTimeout() {
	
	$timeout = getConfig(SESSION_TIMEOUT);
	$formatted = '';

	if ($timeout >= 60) {
		$timeout /= 60;
		if ($timeout >= 24) {
			$timeout /= 24;
			$formatted = $timeout . ' day';
		} else {
			$formatted = $timeout . ' hour';
		}
	} else {
		$formatted = $timeout . ' minute';
	}

	if ($timeout > 1) {
		$formatted .= 's';
	}
	return $formatted;
}