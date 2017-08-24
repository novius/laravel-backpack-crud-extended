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
    private $tmpImages = [];

    /**
     * Hook into the Eloquent model events to upload or delete image
     */
    public static function bootUploadableImage()
    {
        static::observe(app(UploadImageObserver::class));
    }

    public function setUploadedImage($value, $imageAttributeName)
    {
        if ($value === null) {
            \Storage::disk('public')->delete($this->attributes[$imageAttributeName]);
            $this->attributes[$imageAttributeName] = '';
        }

        if (starts_with($value, 'data:image')) {
            $this->tmpImages[$imageAttributeName] = \Image::make($value);
            $this->attributes[$imageAttributeName] = '';
        } elseif (!ends_with($value, '.jpg')) {
            $this->attributes[$imageAttributeName] = '';
        }
    }

    /**
     * @param $path
     * @param $imageAttributeName
     */
    public function fillImagePath($path, $imageAttributeName)
    {
        $this->attributes[$imageAttributeName] = $path;
        // Reset tmpImage to prevent infinite loop
        if (isset($this->tmpImages[$imageAttributeName])) {
            unset($this->tmpImages[$imageAttributeName]);
        }
    }

    public function getTmpImages()
    {
        return $this->tmpImages;
    }

    public function imagesAttributes()
    {
        $uploableImages = $this->uploadableImages();
        if (array_get($uploableImages, 0) === null) {
            $uploableImages = [$uploableImages];
        }

        return array_pluck($uploableImages, 'name');
    }

    public function slugAttributes()
    {
        $uploableImages = $this->uploadableImages();
        if (array_get($uploableImages, 0) === null) {
            $uploableImages = [$uploableImages];
        }

        return array_pluck($uploableImages, 'slug', 'name');
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
