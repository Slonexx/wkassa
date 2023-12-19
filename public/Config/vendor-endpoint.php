<?php

require_once 'lib.php';

$method = $_SERVER['REQUEST_METHOD'];
$queryString = $_SERVER['QUERY_STRING'];

parse_str($queryString, $params);
$appId = "672c9f92-0f5c-4eef-8ec1-1ea737be7515"; // Замените 'appId' на ваш параметр
$accountId = $params['accountId'] ?? null; // Замените 'accountId' на ваш параметр


$app = AppInstanceContoller::load($appId, $accountId);
$replyStatus = true;

switch ($method) {
    case 'PUT':
        $requestBody = file_get_contents('php://input');

        $data = json_decode($requestBody);

        $appUid = $data->appUid;
        $accessToken = $data->access[0]->access_token;

        if (!$app->getStatusName()) {
            $app->TokenMoySklad = $accessToken;
            $app->status = AppInstanceContoller::SETTINGS_REQUIRED;
            $app->persist();

        }
        $url = 'https://smartwebkassa.kz/setAttributes/' . $accountId . '/' . $accessToken;
        $install = file_get_contents($url);
        break;
    case 'GET':
        break;
    case 'DELETE':
        //Тут так же
        $url = 'https://smartwebkassa.kz/delete/'.$accountId;
        $install = file_get_contents($url);

        $replyStatus = false;
        break;
}

if (!$app->getStatusName()) {
    http_response_code(404);
} else if ($replyStatus) {
    header("Content-Type: application/json");
    echo '{"status": "' . $app->getStatusName() . '"}';
}


