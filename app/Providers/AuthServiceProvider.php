<?php

namespace App\Providers;

use App\Models\Rider;
use App\Policies\RiderPolicy;
use App\Models\Account; // <-- Añadir
use App\Policies\AccountPolicy; // <-- Añadir
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Rider::class => RiderPolicy::class,
        Account::class => AccountPolicy::class, // <-- Añadir esta línea
    ];

    public function boot(): void
    {
        //
    }
}
