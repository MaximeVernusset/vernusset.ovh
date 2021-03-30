<?php
checkIsConnected();

httpCode(HTTP_NOT_FOUND, $title, $view);

require_once VIEW_DIR.'page.v.php';

deconnectIfNeeded();