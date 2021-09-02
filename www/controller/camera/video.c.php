<?php
define('CAM_NAME', 'name');
define('CAM_URL_FORMAT', 'http://%s?user=%s&password=%s');

checkIsConnected();

$title = 'Video';
$view = 'camera/video.v.php';
if (!hasAuthorities([VIDEO])) {
	httpCode(HTTP_FORBIDDEN, $title, $view);
} else {
	$cameraGroups = loadVideos();
	foreach ($cameraGroups as $i => $group) {
		foreach ($group[CAMERAS] as $j => $camera) {
			$camFilename = sprintf(CAMERA_URL_OVERRIDE_FILE_FORMAT, CAMERAS_DIR.$group['groupName'], $camera[CAM_NAME]);
			if (file_exists($camFilename)) {
				$cameraGroups[$i][CAMERAS][$j][URL] = file_get_contents($camFilename);
			}
		}
	}
}

function loadVideos() {
	$cameraGroups = json_decode(file_get_contents(CONFIG_DIR.CAMERAS_CONFIG_FILE), true);
	return array_filter($cameraGroups, function($group) {
		return in_array(getUser(), $group['allowedUsers']);
	});
}