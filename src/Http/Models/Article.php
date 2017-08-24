<?php

namespace Novius\Backpack\NewsCRUD\Http\Models;

use Backpack\CRUD\ModelTraits\SpatieTranslatable\HasTranslations;
use Backpack\CRUD\ModelTraits\SpatieTranslatable\Sluggable;
use Novius\Backpack\CRUD\ModelTraits\SpatieTranslatable\SluggableScopeHelpers;
use Novius\Backpack\CRUD\ModelTraits\UploadableImage;

class Article extends \Backpack\NewsCRUD\app\Models\Article
{
    use Sluggable, SluggableScopeHelpers;
    use HasTranslations;
    use UploadableImage;

    protected $fillable = ['slug', 'title', 'content', 'image', 'status', 'category_id', 'featured', 'date', 'thumbnail'];
    protected $translatable = ['slug', 'title', 'content'];

    public function setImageAttribute($value)
    {
        $this->setUploadedImage($value);
    }

    public function uploadableImage()
    {
        return [
            'name' => 'image',
            'slug' => 'title',
        ];
    }
}
