<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DeleteVendorApiController extends Controller
{
    public function delete($accountId){

        try {
            $path = public_path().'/Config/data/'.$accountId.'.json';
            unlink($path);
        } catch (BadResponseException){

        }
        return response(200);

    }
}
