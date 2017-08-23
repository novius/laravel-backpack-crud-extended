<?php

namespace Novius\Backpack\CRUD;

use Backpack\CRUD\CrudServiceProvider as BackpackCrudServiceProvider;
use Novius\Backpack\CRUD\Observers\UploadImageObserver;
use Novius\Backpack\CRUD\Services\UploadImageService;

class CrudServiceProvider extends BackpackCrudServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        /*
         * Allows this package to override backpack views (Project > Novius\Backpack\CRUD > Backpack\CRUD)
         *
         * For instance, when view "crud::foo" is called, Laravel will try to load theses files (in this order):
         * - resources/views/vendor/backpack/crud/foo.blade.php
         * - vendor/novius/backpack-crud/extended/resources/views/foo.blade.php
         * - vendor/backpack/crud/resources/views/foo.blade.php
         */
        $this->loadViewsFrom(resource_path('views/vendor/backpack/crud'), 'crud');
        $this->loadViewsFrom(realpath(dirname(__DIR__).'/resources/views'), 'crud');
        parent::boot();

        /*
         * Add a new namespace "backpackcrud", to allow bypassing overrided views
         *
         * For instance, you can called an original backpack view using "backpackcrud::foo"
         */
        $this->loadViewsFrom(realpath(dirname(__DIR__, 3).'/backpack/crud/src/resources/views'), 'backpackcrud');

        /*
         * Publish overrided views
         */
        $this->publishes([dirname(__DIR__).'/resources/views' => resource_path('views/vendor/backpack/crud')], 'views');

        /*
         * Overrides original CrudPanel
         * Now, Novius\Backpack\CRUD\CrudPanel is automatically used by all backpack controllers
         */
        app()->bind(\Backpack\CRUD\CrudPanel::class, function () {
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

        $this->app->singleton(UploadImageObserver::class, function ($app) {
            return new UploadImageObserver(new UploadImageService(), $app['events']);
        });
    }
}
