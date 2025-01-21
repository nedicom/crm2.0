<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use DefStudio\Telegraph\Controllers\BotController;
//use App\Http\Controllers\BotController;




/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/bot', [BotController::class, 'handle'])->name('bot.api');

