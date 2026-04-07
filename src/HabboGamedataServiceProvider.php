<?php

namespace Habbo\Gamedata;

use Habbo\Gamedata\Console\Commands\ImportExternalOverrideTexts;
use Habbo\Gamedata\Console\Commands\ImportExternalTexts;
use Habbo\Gamedata\Console\Commands\ImportExternalVariables;
use Habbo\Gamedata\Console\Commands\ImportFigureData;
use Habbo\Gamedata\Console\Commands\ImportFurniData;
use Habbo\Gamedata\Console\Commands\ImportProductData;
use Illuminate\Support\ServiceProvider;

class HabboGamedataServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/habbo-gamedata.php', 'habbo-gamedata');
    }

    public function boot(): void
    {
        if ((bool) $this->app['config']->get('habbo-gamedata.routes.auto_register', true)) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/gamedata.php');
        }

        $this->publishes([
            __DIR__ . '/../config/habbo-gamedata.php' => config_path('habbo-gamedata.php'),
        ], 'habbo-gamedata-config');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'habbo-gamedata-migrations');

        $this->publishes([
            __DIR__ . '/../routes/gamedata.php' => base_path('routes/habbo-gamedata.php'),
        ], 'habbo-gamedata-routes');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ImportExternalTexts::class,
                ImportExternalVariables::class,
                ImportExternalOverrideTexts::class,
                ImportProductData::class,
                ImportFigureData::class,
                ImportFurniData::class,
            ]);
        }
    }
}
