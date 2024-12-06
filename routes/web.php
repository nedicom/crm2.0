<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\LawyersController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\LeadsController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\SourceController;
use App\Http\Controllers\DogovorController;
use App\Http\Controllers\GetclientAJAXController;
use App\Http\Controllers\TaskAJAXController;
use App\Http\Controllers\BotController;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use App\Http\Controllers\CsvController;
use App\Http\Controllers\YandexmapController;

Auth::routes();

Route::get('/verify/{token}', 'Auth\RegisterController@verify')->name('register.verify');

Route::post('/bots/staff', \App\Http\Controllers\Bots\StaffController::class)->name('bots.staff');
Route::post('/bot', [BotController::class, 'index'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->name('bot');

Route::any('mail', \App\Http\Controllers\MailController::class)->name('mail');

Route::get('/', function () {
    return redirect('/home');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('auth');
Route::get('/contacts', function () {return view('contacts');})->middleware('auth');

// iCalendar
Route::get('/calendar/create', [\App\Http\Controllers\iCalendar\ManageController::class, 'create'])->name('calendar.create');
Route::get('/calendar/{userID}/calendar.ics', [\App\Http\Controllers\iCalendar\ManageController::class, 'browse'])->name('calendar.browse');

// Сервис Мои звонки
Route::post('/mycalls/subscribe/call', [\App\Http\Controllers\ServicesApi\MyCallsController::class, 'subscribeTrackingCalls'])->name('mycalls.subscribe.call');
Route::post('/mycalls/unsubscribe/call', [\App\Http\Controllers\ServicesApi\MyCallsController::class, 'unsubscribeTrackingCalls'])->name('mycalls.unsubscribe.call');
Route::post('/mycalls/action/call-start', [\App\Http\Controllers\ServicesApi\MyCallsController::class, 'actionCallStart'])->name('mycalls.action.call_start');
Route::post('/mycalls/action/call-finished', [\App\Http\Controllers\ServicesApi\MyCallsController::class, 'actionCallFinished'])->name('mycalls.action.call_finished');
Route::post('/mycalls/download-log', [\App\Http\Controllers\ServicesApi\MyCallsController::class, 'downloadLogFile'])->name('mycalls.download_log');

Route::middleware(['auth'])->group(function () {
    Route::post('/yandexmap', [YandexmapController::class, 'create'])->name('yandexmap');
    
    Route::controller(LawyersController::class)->group(function () {
        Route::post('/avatar/add', 'addavatar')->name('add-avatar');
    });

    Route::controller(DogovorController::class)->group(function () {
        Route::get('/contracts', 'index')->name('contracts.index');
        Route::post('/dogovor/add', 'store')->name('adddogovor');
        Route::get('/dogovor/{id}', 'showdogovorById')->name('showdogovorById');
        Route::post('/dogovor/{id}/edit', 'dogovorUpdateSubmit')->name('dogovorUpdateSubmit');
    });

    Route::controller(SourceController::class)->group(function () {
        Route::post('/source/add', 'addSource')->name('addSource');
    });

    Route::controller(LeadsController::class)->group(function () {
        Route::get('/leads', 'showleads')->name('leads');
        Route::get('/oldleads', 'showoldleads')->name('oldleads');
        Route::get('/leadanalitics', 'leadanalitics')->name('leadanalitics');
        Route::post('/leads/add', 'addlead')->name('addlead');
        Route::get('/leads/{id}', 'showLeadById')->name('showLeadById');
        Route::post('/leads/{id}/edit', 'LeadUpdateSubmit')->name('LeadUpdateSubmit');
        Route::post('/leads/{id}/delete', 'leadDelete')->name('leadDelete');
        Route::post('/leads/{id}/towork', 'leadToWork')->name('leadToWork');
        Route::post('/leads/{id}/toclient', 'leadToClient')->name('leadToClient');
    });

    Route::controller(ClientsController::class)->group(function () {
        Route::get('/clients', 'index')->name('clients');
        Route::post('/clients/add', 'store')->name('add-client')->withoutMiddleware([ConvertEmptyStringsToNull::class]);
        Route::get('/clients/{id}', 'show')->name('showClientById');
        Route::post('/clients/{id}/edit', 'update')->name('Client-Update-Submit');
        Route::get('/clients/{id}/delete', 'delete')->name('Client-Delete');
    });

    Route::controller(TasksController::class)->group(function () {
        Route::get('/tasks', 'index')->name('tasks');
        Route::post('/tasks/add', 'store')->name('addtask');
        Route::post('/tasks/add-by-lead', 'storeByLead')->name('add.task.lead');
        Route::post('/tasks/add/tag', 'tag')->name('tag');
        Route::get('/tasks/{id}', 'showTaskById')->name('showTaskById');
        Route::post('/tasks/{id}/edit', 'editTaskById')->name('editTaskById');
        Route::get('/tasks/{id}/delete', 'delete')->name('TaskDelete');
        Route::get('/tasks/{id}/complete', 'complete')->name('TaskComplete');
        Route::post('/tasks/get-deals', 'getDealsByClient')->name('task.get.deals');
        Route::post('/tasks/list/ajax', 'getAjaxList')->name('tasks.list.ajax');
    });

    Route::get('/deal/{id}', [DealController::class, 'show'])->name('deal.show');
    Route::post('/deal/create', [DealController::class, 'store'])->name('deal.store');
    Route::post('/deal/{id}/update', [DealController::class, 'update'])->name('deal.update');
    Route::post('/deal/{id}/delete', [DealController::class, 'delete'])->name('deal.delete');

    Route::resource('services', ServicesController::class)->except('show', 'edit')->middleware(['auth', 'can:manage-services']);
    Route::post('/services/edit/{service}', [ServicesController::class, 'ajaxEdit'])->name('services.edit')->middleware(['auth', 'can:manage-services']);
    Route::get('/services/ajax/list', [ServicesController::class, 'ajaxList'])->name('services.list.ajax')->middleware('auth');
    Route::post('/services/ajax/element', [ServicesController::class, 'ajaxElement'])->name('services.element.ajax')->middleware('auth');
    Route::get('/services/ajax/search', [ServicesController::class, 'ajaxSearch'])->name('services.search.ajax')->middleware('auth');

    Route::controller(PaymentsController::class)->group(function () {
        Route::get('/payments', 'showpayments')->name('payments');
        Route::post('/payments/add', 'addpayment')->name('addpayment');
        Route::get('/payments/{id}', 'showPaymentById')->name('showPaymentById');
        Route::post('/payments/{id}/edit', 'PaymentUpdateSubmit')->name('PaymentUpdateSubmit');
        Route::get('/payments/{id}/delete', 'PaymentDelete')->name('PaymentDelete');
        Route::post('/payments/list/ajax', 'getAjaxList')->name('payments.list.ajax');
    });

    Route::get('/lawyers', [LawyersController::class, 'Alllawyers'])->name('lawyers');
    Route::post('/lawyers/add', [LawyersController::class, 'submit'])->name('add-lawyer');

    Route::resource('users', \App\Http\Controllers\UsersController::class);
    Route::post('/users/{user}/verify', 'UsersController@verify')->name('users.verify');
    Route::put('/users/{user}/change-password', [\App\Http\Controllers\UsersController::class, 'changePassword'])->name('users.change-password');

    // Генерация документов
    Route::post('generate/cert-completion/{client}', [\App\Http\Controllers\GenerateDocumentController::class, 'certificateCompletion'])->name('client.generate.document');
});

Route::post('/getclient', [GetclientAJAXController::class, 'getclient'])->name('getclient')->middleware('auth');

Route::post('/setstatus', [TaskAJAXController::class, 'setstatustask'])->name('setstatus');

Route::post('/metrika', [CsvController::class, 'leads'])->name('metrikaleads');
