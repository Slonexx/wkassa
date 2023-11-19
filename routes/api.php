<?php

use App\Http\Controllers\integration\connectController;
use App\Http\Controllers\WebhookMSController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/webhook/customerorder/',[WebhookMSController::class, 'customerorder']);
Route::post('/webhook/demand/',[WebhookMSController::class, 'customerorder']);
Route::post('/webhook/salesreturn/',[WebhookMSController::class, 'customerorder']);


Route::group(["prefix" => "integration"], function () {
    Route::get('client/connect/{accountId}', [connectController::class, 'connectClient']);
});
