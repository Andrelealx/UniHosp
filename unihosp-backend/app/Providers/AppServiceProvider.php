<?php

namespace App\Providers;

use App\Models\Atendimento;
use App\Models\Paciente;
use App\Models\Painel;
use App\Models\Senha;
use App\Models\Triagem;
use App\Policies\AtendimentoPolicy;
use App\Policies\PacientePolicy;
use App\Policies\PainelPolicy;
use App\Policies\SenhaPolicy;
use App\Policies\TriagemPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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
        Gate::policy(Paciente::class, PacientePolicy::class);
        Gate::policy(Senha::class, SenhaPolicy::class);
        Gate::policy(Triagem::class, TriagemPolicy::class);
        Gate::policy(Atendimento::class, AtendimentoPolicy::class);
        Gate::policy(Painel::class, PainelPolicy::class);

        Gate::before(function ($user, string $ability): ?bool {
            if ($user->hasRole('administrador')) {
                return true;
            }

            return null;
        });

        RateLimiter::for('login', function (Request $request): array {
            return [
                Limit::perMinute(10)->by((string) $request->ip()),
            ];
        });
    }
}
