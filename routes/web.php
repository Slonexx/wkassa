<?php

use App\Http\Controllers\AttributeController;
use App\Http\Controllers\Config\collectionOfPersonalController;
use App\Http\Controllers\Config\DeleteVendorApiController;
use App\Http\Controllers\Config\vendorEndpoint;
use App\Http\Controllers\Entity\PopapController;
use App\Http\Controllers\Entity\PrintController;
use App\Http\Controllers\Entity\widgetController;
use App\Http\Controllers\errorSettingController;
use App\Http\Controllers\initialization\indexController;
use App\Http\Controllers\Setting\AccessController;
use App\Http\Controllers\Setting\AutomationController;
use App\Http\Controllers\Setting\ChangeController;
use App\Http\Controllers\Setting\CreateAuthTokenController;
use App\Http\Controllers\Setting\DocumentController;
use App\Http\Controllers\Setting\ReportController;
use Illuminate\Support\Facades\Route;


//main windows
Route::get('/', [indexController::class, 'initialization']);
Route::get('/{accountId}/', [indexController::class, 'index'])->name('main');

Route::put('/Config/vendor-endpoint.php', [vendorEndpoint::class, 'put']);
Route::delete('/Config/vendor-endpoint.php', [vendorEndpoint::class, 'delete']);

Route::get('/search/employee/byName/{login}', [indexController::class, 'searchEmployeeByID']);

//Setting
Route::get('/Setting/createAuthToken/{accountId}', [CreateAuthTokenController::class, 'getCreateAuthToken']);
Route::post('/Setting/createAuthToken/{accountId}', [CreateAuthTokenController::class, 'postCreateAuthToken']);
Route::get('/Setting/Create/AuthToken/{accountId}', [CreateAuthTokenController::class, 'createAuthToken']);


Route::get('/Setting/Document/{accountId}', [documentController::class, 'getDocument'])->name('getDocument');
Route::post('/Setting/Document/{accountId}', [documentController::class, 'postDocument']);


Route::get('/Setting/Worker/{accountId}', [AccessController::class, 'getWorker'])->name('getWorker');
Route::post('/Setting/Worker/{accountId}', [AccessController::class, 'postWorker']);

Route::get('/Setting/Automation/{accountId}', [AutomationController::class, 'getAutomation'])->name('getAutomation');
Route::post('/Setting/Automation/{accountId}', [AutomationController::class, 'postAutomation']);


Route::get('/kassa/change/{accountId}', [ChangeController::class, 'getChange']);
Route::get('/kassa/MoneyOperation/{accountId}', [ChangeController::class, 'MoneyOperation']);
Route::get('/kassa/MoneyOperation/viewCash/{accountId}', [ChangeController::class, 'viewCash']);
Route::get('/kassa/XReport/{accountId}', [ReportController::class, 'XReport']);
Route::get('/kassa/ZReport/{accountId}', [ReportController::class, 'ZReport']);
Route::get('/Setting/error/{accountId}', [errorSettingController::class, 'getError'])->name('errorSetting');





//Widget
Route::get('/widget/{object}', [widgetController::class, 'widgetObject']);
Route::get('/widget/Info/Attributes', [widgetController::class, 'widgetInfoAttributes']);
Route::get('LOG/widget/Info/Attributes', [widgetController::class, 'LOG_widgetInfoAttributes']);


//Popup
Route::get('/Popup/{object}', [PopapController::class, 'Popup']);
Route::get('/Popup/{object}/show', [PopapController::class, 'showPopup']);
Route::post('/Popup/{object}/send', [PopapController::class, 'sendPopup']);


//
Route::post('/Test/{object}/send', [PopapController::class, 'TestSendPopup']);
Route::get('/Popup/print/{accountId}/{entity_type}/{object}', [PrintController::class, 'PopupPrint']);


//
Route::get('delete/{accountId}/', [DeleteVendorApiController::class, 'delete']);
Route::get('setAttributes/{accountId}/{tokenMs}', [AttributeController::class, 'setAllAttributesVendor']);


//для админа
Route::get('/web/getPersonalInformation/', [collectionOfPersonalController::class, 'getPersonal']);
Route::get('/collectionOfPersonalInformation/{accountId}/', [collectionOfPersonalController::class, 'getCollection']);
