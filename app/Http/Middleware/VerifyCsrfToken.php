<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/attach',
        '/logistic/map/new',
        '/logistic/map/update',
        '/logistic/map/many',
        '/logistic/map/get_equipment',
        '/ajax/lid/update_field'
//        '/display/logistic_map',
    ];
}
