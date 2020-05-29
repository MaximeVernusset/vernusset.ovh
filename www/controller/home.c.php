<?php
checkIsConnected();
checkSessionValidity();

$title = 'vernusset.ovh';
$view = VIEW_DIR.'home.v.php';

require_once VIEW_DIR.'/page.v.php';

deconnectIfNeeded();