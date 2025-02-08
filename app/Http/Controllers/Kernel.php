<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

Class Kernel extends HttpKernel
{
    protected $middlewareGroups = [
        'web' => [
            \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,
            \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
        ],
    ];
    
}
