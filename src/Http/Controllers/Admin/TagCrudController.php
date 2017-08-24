<?php

namespace Novius\Backpack\NewsCRUD\Http\Controllers\Admin;

use Novius\Backpack\NewsCRUD\Http\Models\Tag;

class TagCrudController extends \Backpack\NewsCRUD\app\Http\Controllers\Admin\TagCrudController
{
    public function __construct()
    {
        parent::__construct();
        $this->crud->setModel(Tag::class);
    }
}
