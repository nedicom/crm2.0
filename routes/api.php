<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
//use DefStudio\Telegraph\Controllers\BotController;
use App\Http\Controllers\AvitoBotController;
//use App\Http\Controllers\BotController;

use App\Http\Controllers\Api\CrmApiController;

Route::middleware(['api.auth', 'throttle:60,1'])->group(function () {
    Route::get('/client/{id}/summary', [CrmApiController::class, 'clientSummary']);
});

//avito integration
Route::post('/avito/postmessage', [AvitoBotController::class, 'postmessage']);
Route::post('/avito/getmessage', [AvitoBotController::class, 'getmessage']);

Route::get('/avito/registerwebhook', [AvitoBotController::class, 'registerWebhook']);

Route::get('/avito/getmessages', [AvitoBotController::class, 'showChats']);



