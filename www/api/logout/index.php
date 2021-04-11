<?php
require_once __DIR__.'/../api.php';

clearSessionAndCookie();
http_response_code(HTTP_OK);
$response[MESSAGE] = 'Logged out';
echo json_encode($response);