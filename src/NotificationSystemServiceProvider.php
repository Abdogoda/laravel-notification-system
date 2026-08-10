<?php

namespace NotificationSystem;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use NotificationSystem\Channels\ChannelManager;
use NotificationSystem\Contracts\DeliveryLoggerInterface;
use NotificationSystem\Contracts\RecipientResolverInterface;
use NotificationSystem\Resolvers\RecipientResolver;
use NotificationSystem\Services\DeliveryLogger;
use NotificationSystem\Services\NotificationManager;

/**
 * Service provider for the NotificationSystem package.
 *
 * Registers all bindings, merges config, loads migrations/routes/views/translations,
 * and sets up publishable assets for the host application.
 */
class NotificationSystemServiceProvider extends ServiceProvider
{
    /**
     * Register package services into the container.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/notification-system.php',
            'notification-system'
        );

        $this->app->singleton(ChannelManager::class, function () {
            return new ChannelManager();
        });

        $this->app->bind(RecipientResolverInterface::class, RecipientResolver::class);
        $this->app->bind(DeliveryLoggerInterface::class, DeliveryLogger::class);

        $this->app->singleton(NotificationManager::class, function ($app) {
            return new NotificationManager(
                $app->make(ChannelManager::class),
                $app->make(RecipientResolverInterface::class),
                $app->make(DeliveryLoggerInterface::class)
            );
        });
    }

    /**
     * Bootstrap package services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'notification-system');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'notification-system');

        $this->bootBladeComponents();
        $this->bootPublishing();
    }

    /**
     * Register Blade components using the component namespace for auto-discovery.
     */
    protected function bootBladeComponents(): void
    {
        Blade::componentNamespace('NotificationSystem\\View\\Components', 'notification-system');
    }

    /**
     * Register publishable assets for the host application.
     */
    protected function bootPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            // Config
            $this->publishes([
                __DIR__.'/../config/notification-system.php' => config_path('notification-system.php'),
            ], 'notification-system-config');

            // Views
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/notification-system'),
            ], 'notification-system-views');

            // Translations
            $this->publishes([
                __DIR__.'/../resources/lang' => $this->app->langPath('vendor/notification-system'),
            ], 'notification-system-translations');

            // Migrations
            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'notification-system-migrations');

            // Assets
            $this->publishes([
                __DIR__.'/../resources/css' => public_path('vendor/notification-system/css'),
                __DIR__.'/../resources/js' => public_path('vendor/notification-system/js'),
                __DIR__.'/../resources/images' => public_path('vendor/notification-system/images'),
            ], 'notification-system-assets');
        }
    }
}
