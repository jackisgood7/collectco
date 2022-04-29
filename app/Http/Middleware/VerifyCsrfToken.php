<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '/api/users/register',
        '/api/users/login',
        '/api/collection/store',
        '/api/collection/delete',
        '/api/trade/store',
        '/api/trade/update',
        '/api/chat/send',

    ];
}
