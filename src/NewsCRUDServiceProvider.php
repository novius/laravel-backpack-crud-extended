<?php

namespace Novius\Backpack\NewsCRUD;

use Illuminate\Routing\Router;

class NewsCRUDServiceProvider extends \Backpack\NewsCRUD\NewsCRUDServiceProvider
{
    public $routeFilePath = '/../routes/backpack/newscrud.php';

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $this->publishes([__DIR__.'/../routes' => base_path().'/routes'], 'routes');
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function setupRoutes(Router $router)
    {
        // by default, use the routes file provided in vendor
        $routeFilePathInUse = __DIR__.$this->routeFilePath;

        // but if there's a file with the same name in routes/backpack, use that one
        if (file_exists(base_path().$this->routeFilePath)) {
            $routeFilePathInUse = base_path().$this->routeFilePath;
        }

        $this->loadRoutesFrom($routeFilePathInUse);
    }
}
