<?php

namespace Novius\Backpack\CRUD\Tests;

use Novius\Backpack\CRUD\CrudPanel;
use Novius\Backpack\CRUD\CrudServiceProvider;
use Orchestra\Testbench\TestCase;

class CrudPanelTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            CrudServiceProvider::class,
        ];
    }

    /**
     * Default Backpack\CRUD\CrudPanel is overrided by this package class
     */
    public function testCrudPanelIsOverrided()
    {
        $this->assertInstanceOf(CrudPanel::class, app()->make(\Backpack\CRUD\CrudPanel::class));
    }
}
