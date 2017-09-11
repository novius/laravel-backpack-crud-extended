<?php

namespace Novius\Backpack\CRUD\Http\Controllers;

use Novius\Backpack\CRUD\CrudPanel;

class CrudController extends \Backpack\CRUD\app\Http\Controllers\CrudController
{
    public function __construct()
    {
        if (! $this->crud) {
            $this->crud = app()->make(CrudPanel::class);
            // Stores a reference to the current controller
            $this->crud->controller = $this;
            // call the setup function inside this closure to also have the request there
            // this way, developers can use things stored in session (auth variables, etc)
            $this->middleware(function ($request, $next) {
                // Stores a reference to the current request
                $this->crud->request = $this->request = $request;
                $this->setup();
                // Initializes the CRUD permissions
                if (method_exists($this->crud, 'initPermissions')) {
                    $this->crud->initPermissions();
                }

                return $next($request);
            });
        }
    }
}
