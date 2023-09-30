<?php

namespace App\Http\Controllers\Config\Lib;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class cfg extends Controller
{
    public $appId;
    public $appUid;
    public $secretKey;
    public $appBaseUrl;
    public $moyskladVendorApiEndpointUrl;
    public $moyskladJsonApiEndpointUrl;


    public function __construct()
    {
        $this->appId = '672c9f92-0f5c-4eef-8ec1-1ea737be7515';
        $this->appUid = 'webkassa.smartinnovations';
        $this->secretKey = "0rhC5i8gdbSqJ3KnoBjZfci3hnJseq0mGb0QdqEu3PiIM4wp5loQCRMIfIMK5AR5yPJ2OvApvzCzQ9Z81pbYJpYWkp9LlsvvUW8c47q1wJocUZW7GeNxGAqE6AR98RX5";
        $this->appBaseUrl = 'https://smartwebkassa.kz/';
        $this->moyskladVendorApiEndpointUrl = 'https://apps-api.moysklad.ru/api/vendor/1.0';
        $this->moyskladJsonApiEndpointUrl = 'https://api.moysklad.ru/api/remap/1.2';
    }


}
