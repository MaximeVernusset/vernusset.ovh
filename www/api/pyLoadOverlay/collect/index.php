<?php
require_once __DIR__.'/../pyLoadOverlay.php';

session_start();
http_response_code(HTTP_FORBIDDEN);

if (isConnected() && !sessionTimedOut() && hasAuthorities([PYLOAD])) {
	http_response_code(HTTP_BAD_REQUEST);
	$response[MESSAGE] = 'invalid given links';
	if (isset($_POST[LINKS])) {
		$links = parseAndSanitizeInputLinks($_POST[LINKS]);
		$response[DATA]['links'] = $links;
		if (count($links) > 0) {
			http_response_code(HTTP_OK);
			$packageIds = json_decode(postDownloadLinks($links));
			$response[MESSAGE] = 'links collected';
			$response[DATA]['packageIds'] = $packageIds;
			$response[DATA]['linksAndPackageIds'] = array_combine($links, $packageIds);
		}
	}
}

echo json_encode($response);

function parseAndSanitizeInputLinks($links) {
	$sanitized = array();
	$links = preg_split(SPLIT_REGEX, $links);
	foreach ($links as $link) {
		$sanitizedLink = filter_var(htmlentities($link), FILTER_VALIDATE_URL);
		if ($sanitizedLink !== '' && $sanitizedLink !== false) {
			$sanitized[] = $sanitizedLink;
		}
	}
	return $sanitized;
}