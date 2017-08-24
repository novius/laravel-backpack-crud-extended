<?php

namespace Novius\Backpack\NewsCRUD\Http\Controllers\Admin;

use Novius\Backpack\NewsCRUD\Http\Models\Category;

class CategoryCrudController extends \Backpack\NewsCRUD\app\Http\Controllers\Admin\CategoryCrudController
{
    public function __construct()
    {
        parent::__construct();
        $this->crud->setModel(Category::class);
    }
}
