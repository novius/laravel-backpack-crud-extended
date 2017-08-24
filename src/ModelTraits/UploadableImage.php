<?php

namespace Novius\Backpack\CRUD\ModelTraits;

use Novius\Backpack\CRUD\Observers\UploadImageObserver;

/**
 * Trait UploadableImage
 *
 * To make image upload works, just call $this->setUploadedImage($value) on your model attribute mutator
 *
 * @package Novius\Backpack\CRUD\ModelTraits
 */
trait UploadableImage
{
    /**
     * Hook into the Eloquent model events to upload or delete image
     */
    public static function bootUploadableImage()
    {
        static::observe(app(UploadImageObserver::class));
    }

    /**
     * Put value on desired model image attribute
     *
     * @param string $imageAttributeName
     * @param string $path
     */
    public function fillImageUploadedAttributeValue(string $imageAttributeName, string $path)
    {
        $this->{$imageAttributeName} = $path;
    }

    /**
     * Get model attributes name for image upload
     * Simple example: return ['name' => 'image', slug' => 'title'];
     * With multiple images: return [['name' => 'image', slug' => 'title'], ['name' => 'thumbnail', 'slug' => 'title']];
     *
     * @return array
     */
    abstract public function uploadableImages(): array;
}
