<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\LawyersController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\LeadsController;

use App\Http\Controllers\TasksController;
use App\Http\Controllers\SourceController;
use App\Http\Controllers\DogovorController;
use App\Http\Controllers\GetclientAJAXController;
use App\Http\Controllers\TaskAJAXController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\BotController;
use App\Http\Controllers\CalendarController;

Route::post('/bots/staff', \App\Http\Controllers\Bots\StaffController::class)->name('bots.staff');

Route::any('mail', \App\Http\Controllers\MailController::class)->name('mail');

Route::post('/bot', [BotController::class, 'index'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->name('bot');

  Route::get('/', function () {
      return redirect('/home');
  });

  Route::get('/logout', function () {
      return redirect('/login');
  });

  Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('auth');

  Route::get('/contacts', function () {return view('contacts');})->middleware('auth');

  //CalDav for yandex and other
  Route::controller(CalendarController::class)->group(function () {
    Route::get('/calendar/{lawyerid}', 'calendar')->name('calendar');
  });

  Route::middleware(['verified'])->group(function () {

    Route::controller(LawyersController::class)->group(function () {
      Route::post('/avatar/add', 'addavatar')->name('add-avatar');
    });

    Route::controller(DogovorController::class)->group(function () {
      Route::post('/dogovor/add', 'addDogovor')->name('addDogovor');
    });

    Route::controller(DogovorController::class)->group(function () {
      Route::get('/dogovor', 'dogovor')->name('dogovor');
      Route::post('/dogovor/add', 'adddogovor')->name('adddogovor');
      Route::get('/dogovor/{id}', 'showdogovorById')->name('showdogovorById');
      Route::post('/dogovor/{id}/edit', 'dogovorUpdateSubmit')->name('dogovorUpdateSubmit');
    });

    Route::controller(SourceController::class)->group(function () {
      Route::post('/source/add', 'addSource')->name('addSource');
    });

    Route::controller(LeadsController::class)->group(function () {
      Route::get('/leads', 'showleads')->name('leads');
      Route::post('/leads/add', 'addlead')->name('addlead');
      Route::get('/leads/{id}', 'showLeadById')->name('showLeadById');
      Route::post('/leads/{id}/edit', 'LeadUpdateSubmit')->name('LeadUpdateSubmit');
      Route::post('/leads/{id}/delete', 'leadDelete')->name('leadDelete');
      Route::post('/leads/{id}/towork', 'leadToWork')->name('leadToWork');
      Route::post('/leads/{id}/toclient', 'leadToClient')->name('leadToClient');
    });


    Route::controller(ClientsController::class)->group(function () {
      Route::get('/clients', 'AllClients')->name('clients');
      Route::post('/clients/add', 'submit')->name('add-client');
      Route::get('/clients/{id}', 'showClientById')->name('showClientById');
      Route::post('/clients/{id}/edit', 'updateClientSubmit')->name('Client-Update-Submit');
      Route::get('/clients/{id}/delete', 'ClientDelete')->name('Client-Delete');
    });

    Route::controller(TasksController::class)->group(function () {
      Route::get('/tasks', 'index')->name('tasks');
      Route::post('/tasks/add', 'create')->name('addtask');
      Route::post('/tasks/add/tag', 'tag')->name('tag');
      Route::get('/tasks/{id}', 'showTaskById')->name('showTaskById');
      Route::post('/tasks/{id}/edit', 'editTaskById')->name('editTaskById');
      Route::get('/tasks/{id}/delete', 'TaskDelete')->name('TaskDelete');
    });

    Route::get('/services', [ServicesController::class, 'showservices'])->name('showservices')->middleware('auth');
    Route::post('/services/add', [ServicesController::class, 'addservice'])->name('addservice')->middleware('auth');

    Route::controller(PaymentsController::class)->group(function () {
      Route::get('/payments', 'showpayments')->name('payments');
      Route::post('/payments/add', 'addpayment')->name('addpayment');
      Route::get('/payments/{id}', 'showPaymentById')->name('showPaymentById');
      Route::post('/payments/{id}/edit', 'PaymentUpdateSubmit')->name('PaymentUpdateSubmit');
      Route::get('/payments/{id}/delete', 'PaymentDelete')->name('PaymentDelete');
      });

      Route::get('/lawyers', [LawyersController::class, 'Alllawyers'])->name('lawyers');
      Route::post('/lawyers/add', [LawyersController::class, 'submit'])->name('add-lawyer');
  });

   Route::get('/test', [TestController::class, 'test'])->name('test');


   // Route::get('ajax',function() {return view('message');});

    Route::POST('/getclient', [GetclientAJAXController::class, 'getclient'])->name('getclient')->middleware('auth');

    Route::post('/setstatus', [TaskAJAXController::class, 'setstatustask'])->name('setstatus');

    Route::get('/email/verify', function () {
        return view('auth/verify');
    })->middleware('auth')->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/home');
    })->middleware(['auth', 'signed'])->name('verification.verify');


    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('message', 'Ссылка для верификации отправлена');
    })->middleware(['auth', 'throttle:6,1'])->name('verification.send');


Route::get('phpinfo', function () {
    phpinfo();
});

  Auth::routes();
