<?php

namespace App\Services\MetaServices\MetaHook;

use App\Clients\MsClient;

class AttributeHook
{
    public function getProductAttribute($nameAttribute,$apiKey)
    {
        $uri = "https://online.moysklad.ru/api/remap/1.2/entity/product/metadata/attributes";
        $client = new MsClient($apiKey);
        $json = $client->get($uri);
        $foundedMeta = null;
        foreach($json->rows as $row){
            if($row->name == $nameAttribute){
                $foundedMeta = $row->meta;
                break;
            }
        }
        return $foundedMeta;
    }

    public function getOrderAttribute($nameAttribute, $apiKey)
    {
        $uri = "https://online.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes";
        $client = new MsClient($apiKey);
        $json = $client->get($uri);
        $foundedMeta = null;
        foreach($json->rows as $row){
            if($row->name == $nameAttribute){
                $foundedMeta = $row->meta;
                break;
            }
        }
        return $foundedMeta;
    }

    public function getDemandAttribute($nameAttribute, $apiKey)
    {
        $uri = "https://online.moysklad.ru/api/remap/1.2/entity/demand/metadata/attributes";
        $client = new MsClient($apiKey);
        $json = $client->get($uri);
        $foundedMeta = null;
        foreach($json->rows as $row){
            if($row->name == $nameAttribute){
                $foundedMeta = $row->meta;
                break;
            }
        }
        return $foundedMeta;
    }

    public function getSalesReturnAttribute($nameAttribute, $apiKey)
    {
        $uri = "https://online.moysklad.ru/api/remap/1.2/entity/salesreturn/metadata/attributes";
        $client = new MsClient($apiKey);
        $json = $client->get($uri);
        $foundedMeta = null;
        foreach($json->rows as $row){
            if($row->name == $nameAttribute){
                $foundedMeta = $row->meta;
                break;
            }
        }
        return $foundedMeta;
    }

    public function getPaymentInAttribute($nameAttribute, $apiKey)
    {
        $uri = "https://online.moysklad.ru/api/remap/1.2/entity/paymentin/metadata/attributes";
        $client = new MsClient($apiKey);
        $json = $client->get($uri);
        $foundedMeta = null;
        foreach($json->rows as $row){
            if($row->name == $nameAttribute){
                $foundedMeta = $row->meta;
                break;
            }
        }
        return $foundedMeta;
    }

    public function getCashInAttribute($nameAttribute, $apiKey)
    {
        $uri = "https://online.moysklad.ru/api/remap/1.2/entity/cashin/metadata/attributes";
        $client = new MsClient($apiKey);
        $json = $client->get($uri);
        $foundedMeta = null;
        foreach($json->rows as $row){
            if($row->name == $nameAttribute){
                $foundedMeta = $row->meta;
                break;
            }
        }
        return $foundedMeta;
    }

    public function getPaymentOutAttribute($nameAttribute, $apiKey)
    {
        $uri = "https://online.moysklad.ru/api/remap/1.2/entity/paymentout/metadata/attributes";
        $client = new MsClient($apiKey);
        $json = $client->get($uri);
        $foundedMeta = null;
        foreach($json->rows as $row){
            if($row->name == $nameAttribute){
                $foundedMeta = $row->meta;
                break;
            }
        }
        return $foundedMeta;
    }

    public function getCashOutAttribute($nameAttribute, $apiKey)
    {
        $uri = "https://online.moysklad.ru/api/remap/1.2/entity/cashout/metadata/attributes";
        $client = new MsClient($apiKey);
        $json = $client->get($uri);
        $foundedMeta = null;
        foreach($json->rows as $row){
            if($row->name == $nameAttribute){
                $foundedMeta = $row->meta;
                break;
            }
        }
        return $foundedMeta;
    }

    public function getFactureOutAttribute($nameAttribute, $apiKey)
    {
        $uri = "https://online.moysklad.ru/api/remap/1.2/entity/factureout/metadata/attributes";
        $client = new MsClient($apiKey);
        $json = $client->get($uri);
        $foundedMeta = null;
        foreach($json->rows as $row){
            if($row->name == $nameAttribute){
                $foundedMeta = $row->meta;
                break;
            }
        }
        return $foundedMeta;
    }



}
