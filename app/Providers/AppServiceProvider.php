<?php

namespace App\Providers;

//use App\Models\Calificacion;
//use OwenIt\Auditing\Observers\AuditObserver;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

use App\Models\CustomAudit;             // ❶ tu modelo personalizado
use App\Observers\AuditObserver;        // ❷ tu observer (no el de OwenIt)

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale('es');
        CustomAudit::observe(AuditObserver::class);
        //
    }
}
