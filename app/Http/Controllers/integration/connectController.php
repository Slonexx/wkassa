<?php

namespace App\Http\Controllers\integration;


use App\Clients\integrationKassClient;
use App\Clients\MsClient;
use App\Clients\testKassClient;
use App\Http\Controllers\Controller;
use App\Services\ticket\integrationTicketService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class connectController extends Controller
{
    public function connectClient(Request $request, $accountId): JsonResponse
    {

        $URL_WEBKASSA = Config::get("Global");
        if ($accountId == '1dd5bd55-d141-11ec-0a80-055600047495') $url = $URL_WEBKASSA['dev_webkassa'].'api/Authorize';
        else $url = $URL_WEBKASSA['webkassa'].'api/Authorize';

        $client = new Client();
        try {
            $post = $client->post($url, [
                'form_params' => [
                    'login' => $request->email ?? '',
                    'password' => $request->password ?? '',
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


    public function getUrlTicket( $accountId, $ms_token, $kassa_token, $entity_type, $object): Factory|\Illuminate\Contracts\View\View|Application
    {




        $ClientMS = new MsClient($ms_token);



        if ($accountId == '1dd5bd55-d141-11ec-0a80-055600047495') $ClientKassa = new testKassClient($kassa_token);
        else $ClientKassa = new integrationKassClient($kassa_token);


        $ExternalCheckNumber = null;
        try {
            $MS = $ClientMS->get(Config::get("Global")['ms'].$entity_type.'/'.$object);
            foreach ($MS->attributes as $item){
                if ($item->name == "kkm_ID"){
                    $ExternalCheckNumber = $item->value;
                } else continue;
            }
            if ($ExternalCheckNumber != null) $Body = $ClientKassa->TicketPrint($ExternalCheckNumber, $kassa_token);
            else  return view('popup.Print', [
                'StatusCode' => 500,
                'Message' => "Отсутствует информация о дополнительном поле kkm_id, просьба сообщать разработчиком",
                'PrintFormat' => [],
            ]);

            return view('popup.Print', [
                'StatusCode' => 200,
                'Message' => "",
                'PrintFormat' => $Body->Data->Lines,
            ]);

        } catch (BadResponseException $e){
            return view('popup.Print', [
                'StatusCode' => 500,
                'Message' => $e->getMessage(),
                'PrintFormat' => [],
            ]);
        }




    }


    public function sendTicket(Request $request): JsonResponse
    {
        return (new integrationTicketService())->createTicket(json_decode(json_encode($request->all())));
    }
}
