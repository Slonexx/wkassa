<?php

namespace App\Clients;

use App\Http\Controllers\BD\getMainSettingBD;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class KassClient
{
    private Client $client;
    private mixed $URL_WEBKASSA;
    private getMainSettingBD $Setting;

    public function __construct($accountId)
    {
        $this->URL_WEBKASSA = Config::get("Global");
        $this->Setting = new getMainSettingBD($accountId);

        $this->client = new Client([
            'base_uri' => $this->URL_WEBKASSA['webkassa'].'api/',
            'headers' => [
                'x-api-key' => $this->URL_WEBKASSA['token_webkassa'],
                'Content-Type' => 'application/json',
            ]
        ]);
    }

    public function testCheck($body): \Psr\Http\Message\ResponseInterface
    {
        return $this->client->post($this->URL_WEBKASSA['webkassa'].'api/Check',[
            'body' => json_encode($body),
        ]);
    }


    public function apiCashBoxes(): array
    {
        try {
            $result = [];
            $body = [ 'body' => json_encode(['Token'=> $this->Setting->authtoken]) ];
            $tmp = json_decode($this->client->post($this->URL_WEBKASSA['webkassa'].'api/CashBoxes', $body)->getBody()->getContents());
            foreach ($tmp->Data->List as $item){
                $result[] = $item;
            }

            if (property_exists($tmp, 'Errors')){
                return [];
            }
            return $result;
        } catch (BadResponseException $e){
            return [];
        }


    }


    public function MoneyOperation($CashboxUniqueNumber, $OperationType, $Sum){
        $body = [
            "token" => $this->Setting->authtoken,
            "CashboxUniqueNumber" => $CashboxUniqueNumber,
            "OperationType" => (int) $OperationType,
            "Sum" =>(float) $Sum,
            "ExternalCheckNumber" => Str::uuid()->toString(),
        ];

        //dd($body, json_encode($body));

        $res = $this->client->post($this->URL_WEBKASSA['webkassa'].'api/MoneyOperation',[
            'body' => json_encode($body),
        ]);

        return json_decode($res->getBody()->getContents());
    }


    public function XReport($CashboxUniqueNumber){
        $body = [
            "token" => $this->Setting->authtoken,
            "CashboxUniqueNumber" => $CashboxUniqueNumber,
        ];
        $res = $this->client->post($this->URL_WEBKASSA['webkassa'].'api/ZReport',[
            'body' => json_encode($body),
        ]);

        return json_decode($res->getBody()->getContents());
    }

    public function ZReport($CashboxUniqueNumber){
        $body = [
            "token" => $this->Setting->authtoken,
            "CashboxUniqueNumber" => $CashboxUniqueNumber,
        ];
        $res = $this->client->post($this->URL_WEBKASSA['webkassa'].'api/ZReport',[
            'body' => json_encode($body),
        ]);

        return json_decode($res->getBody()->getContents());
    }


    public function postCheck($body){
        $body["token"] = $this->Setting->authtoken;
        $body["CashboxUniqueNumber"] = $this->Setting->CashboxUniqueNumber;

        $res = $this->client->post($this->URL_WEBKASSA['webkassa'].'api/Check',[
            'body' => json_encode($body),
        ]);

        return json_decode($res->getBody());
    }


    public function TicketPrint($ExternalCheckNumber){

        $res = $this->client->post($this->URL_WEBKASSA['webkassa'].'api/Ticket/PrintFormat',[
            'body' => json_encode(
                [
                    "token" => $this->Setting->authtoken,
                    "CashboxUniqueNumber" => $this->Setting->CashboxUniqueNumber,
                    "isDuplicate" => false,
                    "paperKind" => 0,
                    "ExternalCheckNumber" => $ExternalCheckNumber,
                ]
            ),
        ]);

        return json_decode($res->getBody()->getContents());

    }


    public function ShiftHistory($skip, $Take){
        $body = [
            "token" => $this->Setting->authtoken,
            "CashboxUniqueNumber" => $this->Setting->CashboxUniqueNumber,
            "skip" => $skip,
            "Take" => $Take,
        ];
        $res = $this->client->post($this->URL_WEBKASSA['webkassa'].'api/Cashbox/ShiftHistory',[
            'body' => json_encode($body),
        ]);

        return json_decode($res->getBody()->getContents());
    }

}
