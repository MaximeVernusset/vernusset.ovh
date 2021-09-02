<?php
require_once __DIR__.'/../camera.php';

define('CAM_GROUP', 'group');
define('CAM_IP', 'ip');
define('CAM_NAME', 'name');
define('CAM_STREAMING_PORT', 'port');
define('HTTP_USER_AGENT', 'HTTP_USER_AGENT');
define('REQUIRED_USER_AGENTS', ['RPI cam']);

if (isRegistrationRequestValid()) {
    saveCameraIp($_POST[CAM_GROUP], $_POST[CAM_NAME], $_POST[CAM_IP], $_POST[CAM_STREAMING_PORT]);
    http_response_code(HTTP_OK);
    $response[MESSAGE] = 'Camera IP registered';
    $response[DATA][CAM_GROUP] = $_POST[CAM_GROUP];
    $response[DATA][CAM_NAME] = $_POST[CAM_NAME];
    $response[DATA][CAM_IP] = $_POST[CAM_IP];
    $response[DATA][CAM_STREAMING_PORT] = $_POST[CAM_STREAMING_PORT];
} else {    
	http_response_code(HTTP_BAD_REQUEST);
	$response[MESSAGE] = 'Bad request';
}

returnResponse($response);

function isRegistrationRequestValid() {
    if (isset($_POST[CAM_GROUP]) && isset($_POST[CAM_NAME]) && isset($_POST[CAM_IP]) && isset($_POST[CAM_STREAMING_PORT])
            && filter_var($_POST[CAM_IP], FILTER_VALIDATE_IP) && is_numeric($_POST[CAM_STREAMING_PORT])
            && in_array($_SERVER[HTTP_USER_AGENT], REQUIRED_USER_AGENTS)) {
        $cameraGroups = json_decode(file_get_contents(CONFIG_DIR.CAMERAS_CONFIG_FILE), true);
        foreach ($cameraGroups as $group) {
            if ($group['groupName'] == $_POST[CAM_GROUP]) {
                foreach ($group[CAMERAS] as $camera) {
                    if ($camera[CAM_NAME] == $_POST[CAM_NAME]) {
                        return true;
                    }
                }
            }
        }
    }
    return false;
}

function saveCameraIp($group, $name, $ip, $port) {
    if (!file_exists(CAMERAS_DIR)) {
		mkdir(CAMERAS_DIR);
	}
	$camFileName = sprintf(CAMERA_URL_OVERRIDE_FILE_FORMAT, CAMERAS_DIR.$group, $name);
    $camUrl = $ip.':'.$port;
	file_put_contents($camFileName, $camUrl);
}