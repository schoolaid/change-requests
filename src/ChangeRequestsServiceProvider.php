<?php

namespace Said\ChangeRequests;

use Illuminate\Support\ServiceProvider;
use Said\ChangeRequests\Contracts\AuthorizerInterface;
use Said\ChangeRequests\Contracts\ChangeRequestServiceInterface;
use Said\ChangeRequests\Contracts\ContextResolverInterface;
use Said\ChangeRequests\Repositories\ChangeRequestRepository;
use Said\ChangeRequests\Repositories\ChangeRequestRepositoryInterface;
use Said\ChangeRequests\Services\ChangeRequestService;
use Said\ChangeRequests\Support\AllowAuthenticatedAuthorizer;
use Said\ChangeRequests\Support\DefaultContextResolver;
use Said\ChangeRequests\Support\ModuleCatalog;

class ChangeRequestsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/change-requests.php', 'change-requests');

        $this->registerDatabaseConnection();

        // bindIf: si la app registró su propia implementación antes (su provider
        // va primero en config/app.php), la suya gana.
        $this->app->bindIf(ContextResolverInterface::class, DefaultContextResolver::class);
        $this->app->bindIf(AuthorizerInterface::class, AllowAuthenticatedAuthorizer::class);

        $this->app->singleton(ModuleCatalog::class);

        $this->app->bind(ChangeRequestRepositoryInterface::class, ChangeRequestRepository::class);
        $this->app->bind(ChangeRequestServiceInterface::class, ChangeRequestService::class);
    }

    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'change-requests');

        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/change-requests.php' => config_path('change-requests.php'),
            ], 'change-requests-config');
        }
    }

    /**
     * Declara la conexión solo si la app no la definió ella misma: así el paquete
     * funciona con 5 variables de entorno, pero una app que prefiera declararla
     * en config/database.php gana.
     */
    private function registerDatabaseConnection(): void
    {
        $name = (string) config('change-requests.connection');

        if (config("database.connections.{$name}") === null) {
            config(["database.connections.{$name}" => config('change-requests.database')]);
        }
    }
}
