<?php

namespace App\Http\Controllers\Config;

use App\Clients\MsClient;
use App\Http\Controllers\BD\getPersonal;
use App\Http\Controllers\Controller;
use App\Models\userLoadModel;
use App\Services\workWithBD\DataBaseService;
use Illuminate\Http\Request;

class collectionOfPersonalController extends Controller
{
    public function getCollection(Request $request, $accountId): \Illuminate\Http\JsonResponse
    {

        if ($accountId == "ba335056-17e5-11ed-0a80-0fa5000243e5") {
            return response()->json([
                'message' => ""
            ]);
        }


        $Setting = new getSettingVendorController($accountId);
        $getPersonal = new getPersonal($accountId);

        $ClientMS = new MsClient($Setting->TokenMoySklad);
        $object= $ClientMS->get('https://online.moysklad.ru/api/remap/1.2/entity/employee')->rows;
        $email = null;
        $fullName = null;

        foreach ($object as $item){
            if (property_exists($item, 'uid')){
                if (mb_substr($item->uid, 0, 5) == 'admin') {
                    $email = $item->email;
                    $fullName = $item->fullName;
                } else continue;
            }
        }

        if ($getPersonal->email == null){
            DataBaseService::createPersonal($accountId, $email, $fullName, "активированный" );
            $result = 'спасибо, ваши данные теперь есть в системе';
        } else {
            $result = 'выши данные уже есть в системе';
        }

        return response()->json([
            'message' => $result
        ]);

    }

    public function getPersonal(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $model = userLoadModel::all(['accountId', 'email', 'name', 'status'])->all();
        $Personals = null;
        foreach ($model as $item){
            $Personals[] = $item->getAttributes();
        }
        return view("main.getPersonal", ['Personal'=>$Personals]);
    }

}
