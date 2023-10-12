<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        '/bot',
        '/bots/*',
        '/getclient',
        '/tasks/get-deals',
        '/payments/list/ajax',
        '/tasks/list/ajax',
        '/services/ajax/*',
        '/services/edit/*',
        '/calendar/*',
    ];
}
