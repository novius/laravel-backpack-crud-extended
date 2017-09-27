<?php

namespace Novius\Backpack\CRUD\ModelTraits;

use Novius\Backpack\CRUD\Observers\UploadImageObserver;
use Novius\Backpack\CRUD\Services\UploadImageService;

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
        // We need to create a shared instance of Observer (only on this model) to keep some information at each fired events
        $observer = new UploadImageObserver(new UploadImageService());
        app()->instance(UploadImageObserver::class, $observer);

        // Observe Model Events with same instance of observer
        static::observe(app(UploadImageObserver::class));
    }

    /**
     * Put value on desired model image attribute
     *
     * @param string $imageAttributeName
     * @param string $path
     */
    public function fillUploadedImageAttributeValue(string $imageAttributeName, string $path)
    {
        if (method_exists($this, 'isTranslatableAttribute')
            && $this->isTranslatableAttribute($imageAttributeName)
        ) {
            $this->setTranslation($imageAttributeName, (string) request('locale', $this->getLocale()), $path); // Default value is relevant when using seeders or any environment where we dont have acces to "request".
        } else {
            $this->{$imageAttributeName} = $path;
        }
    }

    /**
     * Called after image saved on disk
     *
     * @param string $imageAttributeName
     * @param string $imagePath
     * @param string $diskName
     * @return bool
     */
    public function imagePathSaved(string $imagePath, string $imageAttributeName = null, string $diskName = null)
    {
        return true;
    }

    /**
     * Called after image deleted on disk
     *
     * @param string $imagePath
     * @param string|null $imageAttributeName
     * @param string|null $diskName
     * @return bool
     */
    public function imagePathDeleted(string $imagePath, string $imageAttributeName = null, string $diskName = null)
    {
        return true;
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
