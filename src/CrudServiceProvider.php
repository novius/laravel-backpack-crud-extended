<?php

namespace Novius\Backpack\CRUD;

use \Backpack\CRUD\CrudServiceProvider as BackpackCrudServiceProvider;

class CrudServiceProvider extends BackpackCrudServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Allows this package to extend backpack views (Project > Novius\Backpack\CRUD > Backpack\CRUD)
        $this->loadViewsFrom(resource_path('views/vendor/backpack/crud'), 'crud');
        $this->loadViewsFrom(realpath(__DIR__.'/resources/views'), 'crud');

        parent::boot();

        // Overrides original CrudPanel
        app()->bind(\Backpack\CRUD\CrudPanel::class, function() {
            return new CrudPanel();
        });
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();
    }
}
