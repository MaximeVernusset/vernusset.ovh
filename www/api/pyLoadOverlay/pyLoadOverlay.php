<?php
require_once __DIR__.'/../api.php';

// pyload APIs: https://github.com/pyload/pyload/wiki/module.Api.Api
define('API_ABORT_DOWNLOAD', '/api/stopDownloads');
define('API_ADD_FILES_TO_PACKAGE', '/api/addFiles');
define('API_ADD_PACKAGE_WITH_NAME', '/api/addPackage');
define('API_ADD_PACKAGE_WITHOUT_NAME', '/api/generateAndAddPackages');
define('API_CLEAN_QUEUE', '/api/deleteFinished');
define('API_DELETE_FILE', '/api/deleteFiles');
define('API_GET_CURRENT_DOWNLOADS', '/api/statusDownloads');
define('API_GET_QUEUE', '/api/getQueue');
define('API_GET_QUEUE_DATA', '/api/getQueueData');
define('API_GET_SERVER_CONFIG', '/api/getConfig');
define('API_GET_SERVER_STATUS', '/api/statusServer');
define('API_LOGIN', '/api/login');
define('API_PAUSE_DOWNLOAD', '/api/pauseServer');
define('API_POST_CONFIG', '/json/save_config/general');
define('API_RESTART_FILE', '/api/restartFile');
define('API_START_DOWNLOAD', '/api/unpauseServer');
define('NAME', 'name');
define('PACKAGE_ID', 'pid');
define('PACKAGES_IDS', 'packageIds');
define('PARAM_LIMIT_SPEED', 'download|limit_speed');
define('PARAM_MAX_DOWNLOADS', 'download|max_downloads');
define('PARAM_SPEED_LIMIT', 'download|max_speed');
define('PYLOAD_SESSION', 'pyLoadSession');
define('SESSION', 'session');
define('SPLIT_REGEX', '/(\r\n)|\r|\n/');
define('USERNAME', 'username');

session_start();
http_response_code(HTTP_FORBIDDEN);

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
		$sessionId = httpPost(getPyLoadConfig(URL).API_LOGIN, array(
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
			$downloadQueue = json_decode(httpPost($pyLoadUrl.API_GET_QUEUE, $formData), true);
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
		return json_decode(httpPost($pyLoadUrl.$apiToCall, $formData));
	} else {
		return internalError();
	}
}

function postPyLoadConfig($params = []) {
	if (loginPyLoad()) {
		$stringParams = '';
		foreach ($params as $param => $value) {
			$stringParams .= $param.'='.$value.'&';
		}
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => getPyLoadConfig(URL).API_POST_CONFIG,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => HTTP_POST,
			CURLOPT_POSTFIELDS => $stringParams,
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/x-www-form-urlencoded',
				'Cookie: beaker.session.id='.$_SESSION[PYLOAD_SESSION]
			),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode($response);
	} else {
		return internalError();
	}
}

function simpleCommand($apiToCall) {
	if (loginPyLoad()) {
		return json_decode(httpGet(getPyLoadConfig(URL).$apiToCall, array(SESSION => $_SESSION[PYLOAD_SESSION])), true);
	} else {
		return internalError();
	}
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

function cleanQueue() {
	return simpleCommand(API_CLEAN_QUEUE);
}

function abortDownload($fileId) {
	return simpleCommand(API_ABORT_DOWNLOAD.'?fids=['.$fileId.']');
}

function deleteFile($fileId) {
	return simpleCommand(API_DELETE_FILE.'?fids=['.$fileId.']');
}

function restartFile($fileId) {
	return simpleCommand(API_RESTART_FILE.'?fid='.$fileId);
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