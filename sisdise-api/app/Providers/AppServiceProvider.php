<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Empresa;
use App\Policies\EmpresaPolicy;

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


        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        Gate::policy(Empresa::class, EmpresaPolicy::class);

        Gate::define('ser-admin', function (User $user) {
        return $user->tipo === 'Administrador';
        });

        Gate::define('pode-gerir', function (User $user) {
        // Permite se for Administrador OU Avaliador
        return $user->tipo === 'Administrador' || $user->tipo === 'Avaliador';
        });
    }
}
