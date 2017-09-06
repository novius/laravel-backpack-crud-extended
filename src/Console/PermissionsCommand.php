<?php

namespace Novius\Backpack\CRUD\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Backpack\CRUD\app\Http\Controllers\CrudController;

class PermissionsCommand extends Command
{
    protected $signature = 'permissions:generate';

    protected $description = 'Insert permissions in database for each CRUD controllers.';

    /**
     * Create permissions in database for each CRUD controllers.
     *
     * Available only if Backpack\PermissionManager is installed
     */
    public function handle()
    {
        // Checks if the PermissionManagerServiceProvider exists
        if (! class_exists('Backpack\PermissionManager\PermissionManagerServiceProvider')) {
            return $this->error('Requires the package Backpack\PermissionManager.');
        }

        // Gets all routes
        collect(Route::getRoutes())

            // Groups routes by controller
            ->groupBy(function ($route) {
                list($controller) = explode('@', array_get($route->getAction(), 'controller'));

                return $controller;
            })

            // Keeps only the routes handled by a CRUD controller
            ->filter(function ($routes, $controller) {
                return ! empty($controller) && is_subclass_of($controller, CrudController::class);
            })

            // Creates the permissions
            ->each(function ($routes) {
                $route = $routes->first();
                if (method_exists($route->getController()->crud, 'createMissingPermissions')) {
                    $route->getController()->crud->createMissingPermissions();
                }
            });

        return $this->info('Permissions successfully generated.');
    }
}
