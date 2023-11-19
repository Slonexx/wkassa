<?php

namespace App\Http\Controllers\integration;

use App\Clients\KassClient;
use App\Http\Controllers\Controller;
use App\Http\Controllers\globalObjectController;
use App\Services\AdditionalServices\AttributeService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class connectController extends Controller
{
    public function connectClient(Request $request, $accountId): JsonResponse
    {

        $URL_WEBKASSA = Config::get("Global");
        $url = $URL_WEBKASSA['webkassa'].'api/Authorize';

        $client = new Client();
        try {
            $post = $client->post($url, [
                'form_params' => [
                    'login' => $request->email,
                    'password' => $request->password,
                ],
            ]);

            $content = json_decode($post->getBody()->getContents());

            if (property_exists($content, 'Data')) {
                $result = [
                    'status' => true,
                    'auth_token' => json_decode($post->getBody())->Data->Token,
                ];
            } else {
                $result = [
                    'status' => false,
                    'content' => $content,
                ];
            }


        } catch (BadResponseException $e){
            $result = [
                'status' => false,
                'message' => $e->getMessage(),
                'content' => $e->getResponse()->getBody()->getContents(),
            ];
        }

        return response()->json($result);
    }


}
