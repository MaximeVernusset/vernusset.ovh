<?php
require_once __DIR__.'/../api.php';

define('API_ADD_FILES_TO_PACKAGE','addFiles');
define('API_ADD_PACKAGE_WITH_NAME','addPackage');
define('API_ADD_PACKAGE_WITHOUT_NAME','generateAndAddPackages');
define('NAME','name');
define('PACKAGE_ID','pid');
define('PYLOAD_SESSION','pyLoadSession');
define('SESSION','session');
define('SPLIT_REGEX', '/(\r\n)|\r|\n/');
define('USERNAME', 'username');

function getPyLoadConfig($name) {
	return getConfig($name, PYLOAD_CONFIG_FILE);
}

function loginPyLoad() {
	$sessionId = httpPost(getPyLoadConfig(URL).'/api/login', array(
		USERNAME => getPyLoadConfig(USERNAME),
		PASSWORD => getPyLoadConfig(PASSWORD)
	));
	$_SESSION[PYLOAD_SESSION] = json_decode($sessionId);
	return !filter_var($sessionId, FILTER_VALIDATE_BOOLEAN);
}

function postDownloadLinks($links, $packageName = null) {
	if (loginPyLoad()) {
		$pyLoadUrl = getPyLoadConfig(URL);
		$apiToCall = API_ADD_PACKAGE_WITHOUT_NAME;
		$formData = array(
			SESSION => $_SESSION[PYLOAD_SESSION]
		);

		if ($packageName != null) {
			$downloadQueue = json_decode(httpPost($pyLoadUrl.'/api/getQueue', $formData), true);
			$existingPackageWithSameName = array_filter($downloadQueue, function($package) use ($packageName) {
				return $package[NAME] == $packageName;
			});
			
			if (count($existingPackageWithSameName) > 0) {
				$apiToCall = API_ADD_FILES_TO_PACKAGE;
				$formData[PACKAGE_ID] = array_values($existingPackageWithSameName)[0][PACKAGE_ID];
			} else {
				$apiToCall = API_ADD_PACKAGE_WITH_NAME;
				$formData[NAME] = '"'.$packageName.'"';
			}
		}

		$formData[LINKS] = json_encode($links, JSON_UNESCAPED_SLASHES);
		return httpPost($pyLoadUrl.'/api/'.$apiToCall, $formData);
	} else {
		http_response_code(HTTP_INTERNAL_ERROR);
		return false;
	}
}