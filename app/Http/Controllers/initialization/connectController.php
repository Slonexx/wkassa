<?php

namespace App\Http\Controllers\initialization;

use App\Clients\integrationKassClient;
use App\Clients\KassClient;
use App\Clients\MsClient;
use App\Clients\testKassClient;
use App\Http\Controllers\BD\getMainSettingBD;
use App\Http\Controllers\Controller;
use App\Services\AdditionalServices\AttributeService;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class connectController extends Controller
{
    private mixed $data;

    public function getUrlTicket(Request $request, $accountId, $entity_type, $object): Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {

        $this->data = json_decode(json_encode($request->all()));

        $Setting = json_decode(json_decode($request->connection ?? ''));


        $ClientMS = new MsClient($Setting->ms_token);



        if ($accountId == '1dd5bd55-d141-11ec-0a80-055600047495') $ClientKassa = new testKassClient($this->data->setting_main->kassa_token);
        else $ClientKassa = new integrationKassClient($this->data->setting_main->kassa_token);


        $ExternalCheckNumber = null;
        try {
            $MS = $ClientMS->get(Config::get("Global")['ms'].$entity_type.'/'.$object);
            foreach ($MS->attributes as $item){
                if ($item->name == "kkm_ID"){
                    $ExternalCheckNumber = $item->value;
                } else continue;
            }
            if ($ExternalCheckNumber != null) $Body = $ClientKassa->TicketPrint($ExternalCheckNumber, $this->data->setting_main->serial_number);
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

}
