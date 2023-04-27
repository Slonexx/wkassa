<?php

namespace App\Http\Controllers;

use App\Clients\KassClient;
use App\Http\Controllers\BD\getMainSettingBD;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class errorSettingController extends Controller
{
    public function getError(Request $request, $accountId): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $isAdmin = $request->isAdmin;
        $error = $request->error;


        return view('setting.error', [
            'accountId' => $accountId,
            'isAdmin' => $isAdmin,
            'message' => $error,

        ]);
    }
}
