<?php
require_once __DIR__.'/../pyLoadOverlay.php';

if (isConnected() && hasAuthorities([PYLOAD])) {
	http_response_code(HTTP_BAD_REQUEST);
	$response[MESSAGE] = 'Invalid given links';
	if (isset($_POST[LINKS])) {
		$links = parseAndSanitizeInputLinks($_POST[LINKS]);
		$response[DATA][LINKS] = $links;
		if (count($links) > 0) {
			http_response_code(HTTP_OK);
			$packageName = isset($_POST[PACKAGE_NAME]) ? htmlentities($_POST[PACKAGE_NAME]) : null;
			$packageIds = postDownloadLinks($links, $packageName);
			if (!is_array($packageIds) && $packageIds != false) {
				$packageIds = array($packageIds);
			}
			$response[MESSAGE] = 'Links collected';
			$response[DATA][PACKAGES_IDS] = $packageIds;
		}
	}
}

saveSessionAndReturnResponse($response);

function parseAndSanitizeInputLinks($links) {
	$sanitized = array();
	$links = preg_split(SPLIT_REGEX, $links);
	foreach ($links as $link) {
		$sanitizedLink = filter_var(htmlentities($link), FILTER_VALIDATE_URL);
		if (!empty($sanitizedLink)) {
			$sanitized[] = $sanitizedLink;
		}
	}
	return $sanitized;
}