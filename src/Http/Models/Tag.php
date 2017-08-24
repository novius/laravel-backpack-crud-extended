<?php

namespace Novius\Backpack\NewsCRUD\Http\Models;

use Backpack\CRUD\ModelTraits\SpatieTranslatable\HasTranslations;
use Backpack\CRUD\ModelTraits\SpatieTranslatable\Sluggable;
use Novius\Backpack\CRUD\ModelTraits\SpatieTranslatable\SluggableScopeHelpers;

class Tag extends \Backpack\NewsCRUD\app\Models\Tag
{
    use Sluggable, SluggableScopeHelpers;
    use HasTranslations;

    protected $translatable = ['name'];
}
