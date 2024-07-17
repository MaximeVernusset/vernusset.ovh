<?php
require_once __DIR__.'/../api.php';

// pyload APIs: https://github.com/pyload/pyload/wiki/module.Api.Api
define('API_ABORT_DOWNLOAD', '/json/abort_link?id=%s');
define('API_ADD_FILES_TO_PACKAGE', '/api/add_files/%s');
define('API_ADD_PACKAGE_WITH_NAME', '/api/add_package');
define('API_ADD_PACKAGE_WITHOUT_NAME', '/api/generateAndAddPackages');
define('API_CLEAN_QUEUE', '/api/delete_finished');
define('API_DELETE_FILE', '/api/delete_files/[%s]');
define('API_GET_CURRENT_DOWNLOADS', '/api/statusDownloads');
define('API_GET_QUEUE', '/api/getQueue');
define('API_GET_QUEUE_DATA', '/api/getQueueData');
define('API_GET_SERVER_CONFIG', '/api/getConfig');
define('API_GET_SERVER_STATUS', '/api/statusServer');
define('API_LOGIN', '/api/login');
define('API_MOVE_PACKAGE', '/json/move_package?id=%s&dest=%s');
define('API_PAUSE_DOWNLOAD', '/api/pauseServer');
define('API_POST_CONFIG', '/json/save_config?category=core');
define('API_RESTART_FILE', '/api/restart_file/%s');
define('API_RESTART_SERVER', '/api/restart');
define('API_START_DOWNLOAD', '/api/unpauseServer');
define('AUTHENTICATED', 'authenticated');
define('HEADER_SET_COOKIE', 'Set-Cookie:');
define('NAME', 'name');
define('PACKAGE_ID', 'pid');
define('PACKAGES_IDS', 'packageIds');
define('PARAM_LIMIT_SPEED', 'download|limit_speed');
define('PARAM_MAX_DOWNLOADS', 'download|max_downloads');
define('PARAM_SPEED_LIMIT', 'download|max_speed');
define('PYLOAD_SESSION', 'pyload_session');
define('SESSION', 'session');
define('SPLIT_REGEX', '/(\r\n)|\r|\n/');
define('USERNAME', 'username');

http_response_code(HTTP_FORBIDDEN);

function getPyLoadConfig($name) {
	return getConfig($name, PYLOAD_CONFIG_FILE);
}

function internalError() {
	http_response_code(HTTP_INTERNAL_ERROR);
	return false;
}

function extractPyloadSession($headers) {
	if (strpos($headers, HEADER_SET_COOKIE) !== false) {
		$cookie = str_replace(HEADER_SET_COOKIE, '', $headers);
		$cookieBites = explode(';', $cookie);
		$pyloadSession = explode('=', $cookieBites[0])[1];
		$GLOBALS[CUSTOM_SESSION][PYLOAD_SESSION] = $pyloadSession;
	}
}

function curlResponseHeadersCallback($curl, $headers) {
	extractPyloadSession($headers);
    return strlen($headers);
}

function loginPyLoad() {
	if (isset($GLOBALS[CUSTOM_SESSION][PYLOAD_SESSION])) {
		return $GLOBALS[CUSTOM_SESSION][PYLOAD_SESSION];
	} else {
		$pyloadLoginReponse = httpPost(getPyLoadConfig(URL).API_LOGIN, [], array(
			USERNAME => getPyLoadConfig(USERNAME),
			PASSWORD => getPyLoadConfig(PASSWORD)
		), 'curlResponseHeadersCallback');
		$response = json_decode($pyloadLoginReponse, true);
		return isset($response[AUTHENTICATED]) && $response[AUTHENTICATED];
	}
}

function buildHeader($name, $value) {
	return $name.': '.$value;
}

function buildPyloadSessionCookieHeader() {
	return buildHeader('Cookie', http_build_query(array(
		PYLOAD_SESSION => $GLOBALS[CUSTOM_SESSION][PYLOAD_SESSION]
	)));
}

function postDownloadLinks($links, $packageName = null) {
	if (loginPyLoad()) {
		$pyLoadUrl = getPyLoadConfig(URL);
		$apiToCall = API_ADD_PACKAGE_WITHOUT_NAME;
		$needToMovePackageToQueue = true;
		$headers = [buildPyloadSessionCookieHeader()];
		$formData = [];

		if ($packageName != null) {
			$downloadQueue = json_decode(httpPost($pyLoadUrl.API_GET_QUEUE, $headers), true);
			$existingPackageWithSameName = array_filter($downloadQueue, function($package) use ($packageName) {
				return $package[NAME] == $packageName;
			});
			
			if (count($existingPackageWithSameName) > 0) {
				$apiToCall = sprintf(API_ADD_FILES_TO_PACKAGE, array_values($existingPackageWithSameName)[0][PACKAGE_ID]);
			} else {
				$apiToCall = API_ADD_PACKAGE_WITH_NAME;
				$formData[NAME] = '"'.$packageName.'"';
			}
			$needToMovePackageToQueue = false;
		}

		$formData[LINKS] = json_encode($links, JSON_UNESCAPED_SLASHES);
		$packageIds = json_decode(httpPost($pyLoadUrl.$apiToCall, $headers, $formData));
		if ($needToMovePackageToQueue) {
			movePackageToQueue($packageIds[0]);
		}
		return $packageIds;
	} else {
		return internalError();
	}
}

function postPyLoadConfig($params = []) {
	if (loginPyLoad()) {
		return json_decode(httpPost(getPyLoadConfig(URL).API_POST_CONFIG, [buildPyloadSessionCookieHeader()], $params), true);
	} else {
		return internalError();
	}
}

function simpleCommand($apiToCall) {
	if (loginPyLoad()) {
		return json_decode(httpGet(getPyLoadConfig(URL).$apiToCall, [buildPyloadSessionCookieHeader()]), true);
	} else {
		return internalError();
	}
}

function movePackageToQueue($packageId) {
	return simpleCommand(sprintf(API_MOVE_PACKAGE, $packageId, 1));
}

function getServerStatus() {
	return simpleCommand(API_GET_SERVER_STATUS);
}

function getQueueData() {
	return simpleCommand(API_GET_QUEUE_DATA);
}

function getPyloadServerConfig() {
	return simpleCommand(API_GET_SERVER_CONFIG);
}

function getCurrentDownloads() {
	return simpleCommand(API_GET_CURRENT_DOWNLOADS);
}

function startDownload() {
	return simpleCommand(API_START_DOWNLOAD);
}

function pauseDownload() {
	return simpleCommand(API_PAUSE_DOWNLOAD);
}

function restartServer() {
	return simpleCommand(API_RESTART_SERVER);
}

function cleanQueue() {
	return simpleCommand(API_CLEAN_QUEUE);
}

function abortDownload($fileId) {
	return simpleCommand(sprintf(API_ABORT_DOWNLOAD, $fileId));
}

function deleteFile($fileId) {
	return simpleCommand(sprintf(API_DELETE_FILE, $fileId));
}

function restartFile($fileId) {
	return simpleCommand(sprintf(API_RESTART_FILE, $fileId));
}

function getDownloadConfig() {
	return getPyloadServerConfig()['download'];
}

function limitSpeed($speedLimit) {
	return postPyLoadConfig(array(
		PARAM_SPEED_LIMIT => $speedLimit,
		PARAM_LIMIT_SPEED => $speedLimit > 0
	));
}

function setMaxParallelDownloads($maxParallelDownloads) {
	return postPyLoadConfig(array(
		PARAM_MAX_DOWNLOADS => $maxParallelDownloads
	));
}