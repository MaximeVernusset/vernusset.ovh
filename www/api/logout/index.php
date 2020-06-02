<?php
require_once __DIR__.'/../api.php';

session_start();
session_destroy();
http_response_code(HTTP_OK);
$response[MESSAGE] = 'logged out';
echo json_encode($response);