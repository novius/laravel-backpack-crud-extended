<?php

namespace Novius\Backpack\NewsCRUD\Http\Models;

use Backpack\CRUD\ModelTraits\SpatieTranslatable\HasTranslations;
use Backpack\CRUD\ModelTraits\SpatieTranslatable\Sluggable;
use Novius\Backpack\CRUD\ModelTraits\SpatieTranslatable\SluggableScopeHelpers;

class Category extends \Backpack\NewsCRUD\app\Models\Category
{
    use Sluggable, SluggableScopeHelpers;
    use HasTranslations;

    protected $translatable = ['name', 'slug'];
}
