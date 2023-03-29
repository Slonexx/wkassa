<?php

namespace App\Http\Controllers\Setting;

use App\Clients\KassClient;
use App\Http\Controllers\BD\getMainSettingBD;
use App\Http\Controllers\Config\getSettingVendorController;
use App\Http\Controllers\Config\Lib\AppInstanceContoller;
use App\Http\Controllers\Config\Lib\cfg;
use App\Http\Controllers\Config\Lib\VendorApiController;
use App\Http\Controllers\Controller;
use App\Services\workWithBD\DataBaseService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class CreateAuthTokenController extends Controller
{
    public function getCreateAuthToken(Request $request, $accountId): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $isAdmin = $request->isAdmin;
        $SettingBD = new getMainSettingBD($accountId);

        return view('setting.authToken', [
            'accountId' => $accountId,
            'isAdmin' => $isAdmin,

            'CashboxUniqueNumber'=> $SettingBD->CashboxUniqueNumber,
            'token' => $SettingBD->authtoken,
        ]);
    }

    public function postCreateAuthToken(Request $request, $accountId): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        $Setting = new getSettingVendorController($accountId);
        $SettingBD = new getMainSettingBD($accountId);
        $Client = new KassClient($accountId);

        try {
            $body = $Client->testCheck([
                'Token' => $request->token,
                'CashboxUniqueNumber' => $request->CashboxUniqueNumber,
                'OperationType' => "2",
                'Positions' => [0=>[
                    "Count"=> 1,
                    "Price"=> 0,
                    "TaxPercent"=> 0,
                    "PositionName"=>"Проверка Токена и Заводского номера",
                    "Tax"=> 0,
                    "TaxType"=> 0,
                    "UniCode"=> 796,
                ]],
                'Payments' => [
                    0 => [
                        "sum"=>0,
                        "PaymentType"=>1
                    ]
                ],
                "Roundtype" => 2,
                "ExtenalCheckNumber" => Str::uuid()->toString(),
            ]);

            $result = json_decode($body->getBody()->getContents());

            if (property_exists($result, 'Errors')){
                $message = "Неверный токен или Заводской номер кассы";
                if ($result->Errors[0]->Text == "Продолжительность смены превышает 24 часа. Произведите закрытие смены.") {
                    $message = $result->Errors[0]->Text;
                }

                return view('setting.authToken', [
                    'accountId' => $accountId,
                    'isAdmin' => $request->isAdmin,

                    'message' => $message,
                    'CashboxUniqueNumber'=> $request->CashboxUniqueNumber,
                    'token' => $request->token,
                ]);
            }

            if ($SettingBD->tokenMs == null) {
                DataBaseService::createMainSetting($accountId, $Setting->TokenMoySklad, $request->token, $request->CashboxUniqueNumber);
            } else {
                DataBaseService::updateMainSetting($accountId, $Setting->TokenMoySklad, $request->token, $request->CashboxUniqueNumber);
            }
            $cfg = new cfg();
            $app = AppInstanceContoller::loadApp($cfg->appId, $accountId);
            $app->status = AppInstanceContoller::ACTIVATED;
            $vendorAPI = new VendorApiController();
            $vendorAPI->updateAppStatus($cfg->appId, $accountId, $app->getStatusName());
            $app->persist();

            return to_route('getDocument', ['accountId' => $accountId, 'isAdmin' => $request->isAdmin]);
        } catch (BadResponseException $e){
            return view('setting.authToken', [
                'accountId' => $accountId,
                'isAdmin' => $request->isAdmin,

                'message' => 'ошибка: ' . $e->getCode(),
                'CashboxUniqueNumber'=> $request->CashboxUniqueNumber,
                'token' => $request->token,
            ]);
        }
    }


    public function createAuthToken(Request $request): \Illuminate\Http\JsonResponse
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
            $result = [
                'status' => $post->getStatusCode(),
                'auth_token' => json_decode($post->getBody())->Data->Token,
            ];
        } catch (\Throwable $e){
            $result = [
                'status' => $e->getCode(),
                'auth_token' => null,
            ];
        }

        return response()->json($result);
}
}
