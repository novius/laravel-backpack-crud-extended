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
        // Generates a unique URI path (for cache bursting)
        $uniquePath = $this->generateImageUniqueUriPath($path);

        if ($this->isTranslatableImageAttribute($imageAttributeName)) {
            $this->setTranslation($imageAttributeName, $this->getLocale(), $uniquePath);
        } else {
            $this->setAttribute($imageAttributeName, $uniquePath);
        }
    }

    /**
     * Callback triggered after image saved on disk
     *
     * @param string $imageAttributeName
     * @param string|null $imagePath
     * @param string|null $diskName
     * @return bool
     */
    public function imagePathSaved(string $imagePath, string $imageAttributeName = null, string $diskName = null) : bool
    {
        return true;
    }

    /**
     * Callback triggered after image deleted on disk
     *
     * @param string $imagePath
     * @param string|null $imageAttributeName
     * @param string|null $diskName
     * @return bool
     */
    public function imagePathDeleted(string $imagePath, string $imageAttributeName = null, string $diskName = null) : bool
    {
        return true;
    }

    /**
     * Generates a unique image URI path
     *
     * @param string $path
     * @return string
     */
    public function generateImageUniqueUriPath(string $path)
    {
        $path = preg_replace('/\?v=.*/i', '', $path);
        if (!empty($path)) {
            $path .= '?v='.uniqid();
        }

        return $path;
    }

    /**
     * Checks if the given image attribute name is translatable
     *
     * @param string $imageAttributeName
     * @return bool
     */
    public function isTranslatableImageAttribute(string $imageAttributeName)
    {
        return $this->canTranslateImage() && $this->isTranslatableAttribute($imageAttributeName);
    }

    /**
     * Checks if the model can translate an image
     *
     * @return bool
     */
    public function canTranslateImage()
    {
        return method_exists($this, 'isTranslatableAttribute') && method_exists($this, 'setTranslation');
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
