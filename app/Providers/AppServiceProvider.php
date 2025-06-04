<?php

namespace App\Providers;

//use App\Models\Calificacion;
//use OwenIt\Auditing\Observers\AuditObserver;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

use App\Models\CustomAudit;
use App\Observers\AuditObserver;

// Auditoría automática Inscritos
use App\Models\Inscrito;
use App\Observers\InscripcionStatusObserver;

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

        // Registrar el observer específico para auditoría de cambios de estatus en inscripciones
        Inscrito::observe(InscripcionStatusObserver::class);
    }
}
