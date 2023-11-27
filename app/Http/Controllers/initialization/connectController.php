<?php

namespace App\Http\Controllers\initialization;

use App\Clients\integrationKassClient;
use App\Clients\KassClient;
use App\Clients\MsClient;
use App\Clients\testKassClient;
use App\Http\Controllers\BD\getMainSettingBD;
use App\Http\Controllers\Controller;
use App\Services\AdditionalServices\AttributeService;
use App\Services\ticket\integrationTicketService;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class connectController extends Controller
{
    private mixed $data;



}
