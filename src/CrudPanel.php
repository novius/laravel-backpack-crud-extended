<?php

namespace Novius\Backpack\CRUD;

use \Backpack\CRUD\CrudPanel as BackpackCrudPanel;

class CrudPanel extends BackpackCrudPanel
{
    public function __construct()
    {
        dump(' Novius\Backpack\CRUD\CrudPanel called!');
        parent::__construct();
    }
}
