<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class vendorEndpoint extends Controller
{
    public function put(Request $request){

        $data = json_decode(json_encode($request->all()));

        $method = $_SERVER['REQUEST_METHOD'];
        $queryString = $_SERVER['QUERY_STRING'];

        parse_str($queryString, $params);
        $apps = "672c9f92-0f5c-4eef-8ec1-1ea737be7515"; // Замените 'appId' на ваш параметр
        $accountId = $params['accountId'] ?? null; // Замените 'accountId' на ваш параметр
        $app = Lib::load($apps, $accountId);

        $accessToken = $data->access[0]->access_token;

        if (!$app->getStatusName()) {
            $app->TokenMoySklad = $accessToken;
            $app->status = Lib::SETTINGS_REQUIRED;
            $app->persist();

        }


        if (!$app->getStatusName()) {
            http_response_code(404);
        } else {
            return Response::json([
                'status' => $app->getStatusName()
            ]);
        }
    }

    public function delete(Request $request, $apps, $accountId){

        $data = json_decode(json_encode($request->all()));

        $method = $_SERVER['REQUEST_METHOD'];
        $queryString = $_SERVER['QUERY_STRING'];

        parse_str($queryString, $params);
        $apps = "672c9f92-0f5c-4eef-8ec1-1ea737be7515"; // Замените 'appId' на ваш параметр
        $accountId = $params['accountId'] ?? null; // Замените 'accountId' на ваш параметр


        $app = Lib::load($apps, $accountId);

        $url = 'https://smartwebkassa.kz/delete/'.$accountId;
        $install = file_get_contents($url);



        if (!$app->getStatusName()) {
            http_response_code(404);
        }
    }

}
