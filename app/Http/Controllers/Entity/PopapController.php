<?php

namespace App\Http\Controllers\Entity;

use App\Clients\MsClient;
use App\Http\Controllers\BD\getMainSettingBD;
use App\Http\Controllers\Controller;
use App\Http\Controllers\TicketController;
use App\Services\ticket\TicketService;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\ArrayShape;

class PopapController extends Controller
{
    public function Popup(Request $request, $object): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        /*switch ($object){
            case "customerorder":{
                return view( 'popup.fiscalization', [] );
            }
        }*/
        return view( 'popup.ViewPopap', ['Entity' => $object] );
    }

    public function showPopup(Request $request, $object): \Illuminate\Http\JsonResponse
    {

        $object_Id = $request->object_Id;
        $accountId = $request->accountId;
        $Setting = new getMainSettingBD($accountId);

        $url = "https://online.moysklad.ru/api/remap/1.2/entity/".$object."/".$object_Id;


        $Client = new MsClient($Setting->tokenMs);
        try {
            $Body = $Client->get($url);
        } catch (BadResponseException $e){
            return response()->json(["statusCode" => 500, 'message' => $e->getMessage()]);
        }

        $json = $this->info_object_Id($Body, $Client,  $Setting);
        return response()->json($json);

    }

    #[ArrayShape(["statusCode" => "int", 'id' => "", 'name' => "", 'sum' => "float|int", 'vat' => "array|null", 'attributes' => "null[]", 'products' => "array", 'application' => "array"])] public function info_object_Id(mixed $Body, MsClient $Client, getMainSettingBD $Setting ): array
    {
        $attributes = [ 'ticket_id' => null, ];
        $payment_type = $Setting->payment_type;

        if (property_exists($Body, 'attributes')){
            foreach ($Body->attributes as $item){
                if ($item->name == 'фискальный номер (WebKassa)'){
                    $attributes['ticket_id'] = $item->value;
                    break;
                }
            }
        }

        if ($payment_type == null) $payment_type == "0";

        $vatEnabled = $Body->vatEnabled;
        $vat = null;
        $products = [];
        $positions = $Client->get($Body->positions->meta->href)->rows;

        foreach ($positions as $id=>$item){
            $final = $item->price / 100 * $item->quantity;

            if ($vatEnabled == true) {if ($Body->vatIncluded == false) {
                $final = $item->price / 100 * $item->quantity;
                $final = $final + ( $final * ($item->vat/100) );
            }}
            $uom_body = $Client->get($item->assortment->meta->href);

            if (property_exists($uom_body, 'uom')){
                $propety_uom = true;
                $uom = $Client->get($uom_body->uom->meta->href);
                if (property_exists($uom, 'code' ) and property_exists($uom, 'name' )){
                $uom = ['id' => $uom->code, 'name' => $uom->name];
                } else {
                    $propety_uom = false;
                    $uom = ['id' => 796, 'name' => 'шт'];
                }
            } else {

                if (property_exists($uom_body, 'characteristics')){
                    $check_uom = $Client->get($uom_body->product->meta->href);

                    if ( property_exists($check_uom, 'uom') ) {
                        $propety_uom = true;
                        $uom = $Client->get($check_uom->uom->meta->href);
                        $uom = ['id' => $uom->code, 'name' => $uom->name];
                    } else {
                        $propety_uom = false;
                        $uom = ['id' => 796, 'name' => 'шт'];
                    }
                } else {
                    $propety_uom = false;
                    $uom = ['id' => 796, 'name' => 'шт'];
                }
            }


            $products[$id] = [
                'position' => $item->id,
                'propety' => $propety_uom,
                'name' => $Client->get($item->assortment->meta->href)->name,
                'quantity' => $item->quantity,
                'uom' => $uom,
                'price' => round($item->price / 100, 2) ?: 0,
                'vatEnabled' => $item->vatEnabled,
                'vat' => $item->vat,
                'discount' => round($item->discount, 2),
                'final' => round($final - ( $final * ($item->discount/100) ), 2),
            ];
        }

        if ($vatEnabled == true) {
            $vat = [
                'vatEnabled' => $Body->vatEnabled,
                'vatIncluded' => $Body->vatIncluded,
                'vatSum' => $Body->vatSum / 100 ,
            ];
        };
        return [
            "statusCode" => 200,

            'id' => $Body->id,
            'name' => $Body->name,
            'sum' => $Body->sum / 100,
            'vat' => $vat,
            'attributes' => $attributes,

            'products' => $products,

            'application' => [
                'payment_type' => $payment_type
            ],
        ];
    }




    public function sendPopup(Request $request): \Illuminate\Http\JsonResponse
    {

        $data = $request->all();

        $accountId = $data['accountId'];
        $object_Id = $data['object_Id'];
        $entity_type = $data['entity_type'];

        if ($data['money_card'] === null) $money_card = 0;
        else $money_card = $data["money_card"];
        if ($data['money_cash'] === null) $money_cash = 0;
        else $money_cash = $data['money_cash'];
        $pay_type = $data['pay_type'];

        $total = $data['total'];

        $positions =  json_decode($data['position']) ;
        $position = null;
        foreach ($positions as $id=>$item){

            if ($item != null){
                $position[] = $item;
            } else continue;
        }

        $body = [
            'accountId' => $accountId,
            'id_entity' => $object_Id,
            'entity_type' => $entity_type,

            'money_card' => $money_card,
            'money_cash' => $money_cash,
            'pay_type' => $pay_type,

            'total' => $total,

            'positions' => $position,
        ];



        try {

            return app(TicketService::class)->createTicket($body);

        } catch (\Throwable $e){
            return response()->json($e->getMessage());
        }

    }
}
