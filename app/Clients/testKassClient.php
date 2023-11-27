<?php

namespace App\Clients;

use App\Http\Controllers\BD\getMainSettingBD;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class testKassClient
{
    private Client $client;
    private mixed $URL_WEBKASSA;
    private getMainSettingBD $Setting;
    private string $authtoken;

    public function __construct($authtoken)
    {
        $this->URL_WEBKASSA = Config::get("Global");
        $this->authtoken = $authtoken;

        $this->client = new Client([
            'base_uri' => $this->URL_WEBKASSA['dev_webkassa'].'api/',
            'headers' => [
                'x-api-key' => $this->URL_WEBKASSA['dev_token_webkassa'],
                'Content-Type' => 'application/json',
            ]
        ]);
    }

    public function testCheck($body): \Psr\Http\Message\ResponseInterface
    {
        return $this->client->post($this->URL_WEBKASSA['dev_webkassa'].'api/Check',[
            'body' => json_encode($body),
        ]);
    }


    public function apiCashBoxes(): array
    {
        try {
            $result = [];
            $body = [ 'body' => json_encode(['Token'=> $this->authtoken]) ];
            $tmp = json_decode($this->client->post($this->URL_WEBKASSA['dev_webkassa'].'api/CashBoxes', $body)->getBody()->getContents());
            if (property_exists($tmp, 'Data')) foreach ($tmp->Data->List as $item){ $result[] = $item; }
            else { return []; }

            if (property_exists($tmp, 'Errors')){
                return [];
            }
            return $result;
        } catch (BadResponseException $e){
            return [];
        }


    }


    public function MoneyOperation($serial_number, $OperationType, $Sum){
        $body = [
            "token" =>  $this->authtoken,
            "CashboxUniqueNumber" => $serial_number,
            "OperationType" => (int) $OperationType,
            "Sum" =>(float) $Sum,
            "ExternalCheckNumber" => Str::uuid()->toString(),
        ];

        //dd($body, json_encode($body));

        $res = $this->client->post($this->URL_WEBKASSA['dev_webkassa'].'api/MoneyOperation',[
            'body' => json_encode($body),
        ]);

        return json_decode($res->getBody()->getContents());
    }


    public function XReport($serial_number){
        $body = [
            "token" => $this->authtoken,
            "CashboxUniqueNumber" => $serial_number,
        ];
        $res = $this->client->post($this->URL_WEBKASSA['dev_webkassa'].'api/XReport',[
            'body' => json_encode($body),
        ]);

        return json_decode($res->getBody()->getContents());
    }

    public function ZReport($serial_number){
        $body = [
            "token" =>  $this->authtoken,
            "CashboxUniqueNumber" => $serial_number,
        ];
        $res = $this->client->post($this->URL_WEBKASSA['dev_webkassa'].'api/ZReport',[
            'body' => json_encode($body),
        ]);

        return json_decode($res->getBody()->getContents());
    }


    public function postCheck($body, $serial_number){
        $body["token"] =  $this->authtoken;
        $body["CashboxUniqueNumber"] = $serial_number;

        $res = $this->client->post($this->URL_WEBKASSA['dev_webkassa'].'api/Check',[
            'body' => json_encode($body),
        ]);

        return json_decode($res->getBody());
    }


    public function TicketPrint($ExternalCheckNumber, $serial_number){
        $res = $this->client->post($this->URL_WEBKASSA['dev_webkassa'].'api/Ticket/PrintFormat',[
            'body' => json_encode(
                [
                    "token" => $this->authtoken,
                    "CashboxUniqueNumber" => $serial_number,
                    "isDuplicate" => false,
                    "paperKind" => 0,
                    "ExternalCheckNumber" => $ExternalCheckNumber,
                ]
            ),
        ]);

        return json_decode($res->getBody()->getContents());

    }


    public function ShiftHistory($skip, $Take, $serial_number){
        try {
            $body = [
                "token" => $this->authtoken,
                "CashboxUniqueNumber" => $serial_number,
                "Skip" => $skip,
                "Take" => $Take,
            ];
            $res = $this->client->post($this->URL_WEBKASSA['dev_webkassa'].'api/Cashbox/ShiftHistory',[
                'body' => json_encode($body),
            ]);
        } catch (BadResponseException $e){
            dd($e->getResponse()->getBody()->getContents());
        }

        return json_decode($res->getBody()->getContents());
    }

}
