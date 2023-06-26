<?php

namespace App\Http\Controllers\Setting;

use App\Clients\KassClient;
use App\Http\Controllers\BD\getMainSettingBD;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ChangeController extends Controller
{
    public function getChange(Request $request, $accountId): View|Factory|RedirectResponse|Application
    {
        $isAdmin = $request->isAdmin;

        $SettingBD = new getMainSettingBD($accountId);

        try {
            $Client = new KassClient($accountId);
            if ($SettingBD->authtoken != null){
                $ArrayKassa = $Client->apiCashBoxes();
            }
            else  return to_route('errorSetting', [
                    'accountId' => $accountId,
                    'isAdmin' => $isAdmin,
                    'error' => "Токен приложения отсутствует, сообщите разработчикам приложения"]
            );
        } catch (BadResponseException $e){
            return to_route('errorSetting', [
                    'accountId' => $accountId,
                    'isAdmin' => $isAdmin,
                    'error' => $e->getResponse()->getBody()->getContents()]
            );
        }

        $kassa = $SettingBD->CashboxUniqueNumber;
        return view('main.change', [
            'accountId' => $accountId,
            'isAdmin' => $isAdmin,

            'ArrayKassa'=> $ArrayKassa,
            'kassa' => $kassa,
        ]);

    }


    public function MoneyOperation(Request $request, $accountId): array
    {
        $Client = new KassClient($accountId);
        try {
            $body = $Client->MoneyOperation($request->CashboxUniqueNumber, $request->OperationType, $request->Sum );
            if (property_exists($body, "Errors")) {
                return [
                    'statusCode' => 500,
                    'message' => $body->Errors[0]->Text,
                ];
            }
            $message = "";
            if ($request->OperationType == 1){
                $message = "Изъятие из кассу наличных на сумму: ".$request->Sum.' '.PHP_EOL." Наличных осталось в кассе: ".$body->Data->Sum;
            } elseif ($request->OperationType == 0) {
                $message = "Внесение в кассу наличных на сумму: ".$request->Sum.' '.PHP_EOL." Наличных осталось в кассе: ".$body->Data->Sum;
            }

            return [
                'statusCode' => 200,
                'message' => $message,
            ];
        } catch (BadResponseException $e){
            return [
                'statusCode' => 500,
                'message' => $e->getMessage(),
            ];
        }
    }


    public function viewCash(Request $request, $accountId): array
    {
        $Client = new KassClient($accountId);
        try {
            $body = $Client->MoneyOperation($request->CashboxUniqueNumber, 0, 0 );
            if (property_exists($body, "Errors")) {
                return [
                    'statusCode' => 500,
                    'message' => $body->Errors[0]->Text,
                ];
            }

            return [
                'statusCode' => 200,
                'message' => $body->Data->Sum,
            ];
        } catch (BadResponseException $e){
            return [
                'statusCode' => 500,
                'message' => $e->getMessage(),
            ];
        }
    }
}
