<?php
require_once __DIR__.'/../api.php';

define('API_ADD_FILES_TO_PACKAGE', '/addFiles');
define('API_ADD_PACKAGE_WITH_NAME', '/addPackage');
define('API_ADD_PACKAGE_WITHOUT_NAME', '/generateAndAddPackages');
define('API_CLEAN_QUEUE', '/deleteFinished');
define('API_GET_QUEUE', '/getQueue');
define('API_GET_SERVER_STATUS', '/statusServer');
define('API_LOGIN', '/login');
define('API_PAUSE_DOWNLOAD', '/stopAllDownloads');
define('API_START_DOWNLOAD', '/unpauseServer');
define('NAME', 'name');
define('PACKAGE_ID', 'pid');
define('PACKAGES_IDS', 'packageIds');
define('PATH_API', '/api');
define('PYLOAD_SESSION', 'pyLoadSession');
define('SESSION', 'session');
define('SPLIT_REGEX', '/(\r\n)|\r|\n/');
define('USERNAME', 'username');

function getPyLoadConfig($name) {
	return getConfig($name, PYLOAD_CONFIG_FILE);
}

function internalError() {
	http_response_code(HTTP_INTERNAL_ERROR);
	return false;
}

function loginPyLoad() {
	if (isset($_SESSION[PYLOAD_SESSION])) {
		return $_SESSION[PYLOAD_SESSION];
	} else {
		$sessionId = httpPost(getPyLoadConfig(URL).PATH_API.API_LOGIN, array(
			USERNAME => getPyLoadConfig(USERNAME),
			PASSWORD => getPyLoadConfig(PASSWORD)
		));
		$_SESSION[PYLOAD_SESSION] = json_decode($sessionId);
		return !filter_var($sessionId, FILTER_VALIDATE_BOOLEAN);
	}
}

function postDownloadLinks($links, $packageName = null) {
	if (loginPyLoad()) {
		$pyLoadUrl = getPyLoadConfig(URL);
		$apiToCall = API_ADD_PACKAGE_WITHOUT_NAME;
		$formData = array(
			SESSION => $_SESSION[PYLOAD_SESSION]
		);

		if ($packageName != null) {
			$downloadQueue = json_decode(httpPost($pyLoadUrl.PATH_API.API_GET_QUEUE, $formData), true);
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
		return json_decode(httpPost($pyLoadUrl.PATH_API.$apiToCall, $formData));
	} else {
		return internalError();
	}
}

function simpleCommand($apiToCall) {
	if (loginPyLoad()) {
		return json_decode(httpGet(getPyLoadConfig(URL).PATH_API.$apiToCall, array(SESSION => $_SESSION[PYLOAD_SESSION])));
	} else {
		return internalError();
	}
}

function getServerStatus() {
	return simpleCommand(API_GET_SERVER_STATUS);
}

function startDownload() {
	return simpleCommand(API_START_DOWNLOAD);
}

function pauseDownload() {
	return simpleCommand(API_PAUSE_DOWNLOAD);
}

function cleanQueue() {
	return simpleCommand(API_CLEAN_QUEUE);
}