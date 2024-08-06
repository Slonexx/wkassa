<?php

namespace App\Http\Controllers\initialization;

use App\Clients\MsClient;
use App\Http\Controllers\BD\getPersonal;
use App\Http\Controllers\Config\Lib\VendorApiController;
use App\Http\Controllers\Controller;
use App\Models\settingModel;
use App\Services\Settings\SettingsService;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;

class indexController extends Controller
{
    public function initialization(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        $contextKey = $request->contextKey;
        if ($contextKey == null) {
            return view("main.dump");
        }
        $vendorAPI = new VendorApiController();
        $employee = $vendorAPI->context($contextKey);
        $accountId = $employee->accountId;

        $isAdmin = $employee->permissions->admin->view;

        return to_route('main', [
            'accountId' => $accountId,
            'isAdmin' => $isAdmin,
        ]);
    }

    public function index(Request $request, $accountId): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {

        $isAdmin = $request->isAdmin;
        $getPersonal = new getPersonal($accountId);
        if ($getPersonal->status == "деактивированный" or $getPersonal->status == null){
            $hideOrShow = "show";
        } else  $hideOrShow = "hide";

        return view("main.index" , [
            'accountId' => $accountId,
            'isAdmin' => $isAdmin,
            'hideOrShow' => $hideOrShow,
        ] );

    }

    public function searchEmployeeByID($login): void
    {
        $allSettings = app(SettingsService::class)->getSettings();

        foreach ($allSettings as $setting){

            try {
                $ClientCheckMC = new MsClient($setting->TokenMoySklad);
                $body = $ClientCheckMC->get('https://api.moysklad.ru/api/remap/1.2/entity/employee?filter=uid~'.$login)->rows;

                if ($body!=[]){
                    dd($body);
                }

            } catch (BadResponseException $e) {
                continue;
            }

        }
    }
    public function check2()
    {
        $allSettings = app(SettingsService::class)->getSettings();

        $content = [];
        $VALUES_main = '';
        $VALUES_kassa = '';

        foreach ($allSettings as $setting) {
            $data = [];
            try {
                $ClientCheckMC = new MsClient($setting->TokenMoySklad);
                $body = $ClientCheckMC->get('https://api.moysklad.ru/api/remap/1.2/entity/employee?filter=uid~admin')->rows;
                if (count($body) > 0) {
                    $body = $body[0];

                    $kassa = settingModel::where('accountId', $body->accountId)->get()->first();
                    dd($kassa);


                    $VALUES_main = $VALUES_main . "('" . $body->accountId . "', '" . $setting->TokenMoySklad . "', '" . $body->uid . "', '1', '0', '2024-07-04 12:00:00', '2024-07-11 07:00:00'), " ;
                    $VALUES_kassa = $VALUES_kassa . "(NULL, ' Касса 1', '" . $kassa->CashboxUniqueNumber . "', 'NULL', NULL, " . $kassa->authtoken .", '1', '1', '" . $body->accountId . "', '2024-07-09 12:10:00', '2024-07-09 12:11:00'), ";
                }
            } catch (BadResponseException $e) {
                continue;
            }
        }


        return response()->json([
                "INSERT INTO `main_settings` (`accountId`, `ms_token`, `UID_ms`, `tariff_id`, `is_active`, `created_at`, `updated_at`) VALUES ". $VALUES_main,
                "INSERT INTO `kassa`(`id`, `name`, `serial_number`, `password`, `email`, `kassa_token`, `count_by_account_id`, `is_active`, `main_settings_id`, `created_at`, `updated_at`) VALUES ". $VALUES_kassa,
            ]
        );
    }

}
