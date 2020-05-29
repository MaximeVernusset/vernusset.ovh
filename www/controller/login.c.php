<?php
$redirectUrl = isset($_GET[REDIRECT_URL]) ? urldecode($_GET[REDIRECT_URL]) : DEFAULT_PAGE;

if (isConnected() && !sessionTimedOut()) {
	header(LOCATION_HEADER.$redirectUrl);
	exit;
}

$title = 'Login';
$view = VIEW_DIR.'login.v.php';

require_once VIEW_DIR.'page.v.php';