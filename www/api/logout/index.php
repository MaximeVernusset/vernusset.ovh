<?php
require_once __DIR__.'/../api.php';

session_unset();
session_destroy();
http_response_code(HTTP_OK);
$response[MESSAGE] = 'logged out';
echo json_encode($response);