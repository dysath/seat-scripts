<?php

namespace Denngarr\Seat\SeatScripts;

use Denngarr\Seat\SeatScripts\Commands\InsuranceUpdate;
use Illuminate\Support\ServiceProvider;

class ScriptsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->addCommands();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/Config/seatscripts.config.php',
            'seatscripts.config'
        );

    }

    private function addCommands()
    {
        $this->commands([
            seatScriptsUsersUpdate::class,
        ]);
    }
}
