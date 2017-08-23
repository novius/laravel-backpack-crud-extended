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
    private $tmpImage = null;
    private $imageAttributeName = null;
    private $imageSlugAttributeName = null;

    /**
     * Hook into the Eloquent model events to upload or delete image
     */
    public static function bootUploadableImage()
    {
        static::observe(app(UploadImageObserver::class));
    }

    /**
     * Init Trait properties with model attributes name from uploadableImage() method
     */
    private function initAttributes()
    {
        $this->imageAttributeName = (string) array_get($this->uploadableImage(), 'nameAttribute');
        $this->imageSlugAttributeName = (string) array_get($this->uploadableImage(), 'slugAttribute');

        if (empty($this->imageAttributeName)) {
            throw new \Exception('Trait UploadableImage : nameAttribute is required.');
        }

        if (!array_key_exists($this->imageAttributeName, $this->attributes)) {
            throw new \Exception('Trait UploadableImage : nameAttribute must be a valid attribute name.');
        }
    }

    /**
     * Call this method in your model attribute mutator
     *
     * @param mixed $value : the image uploaded (string base64 encoded or null)
     * @param string $imageAttributeName : the attribute name on your model where to stock the image
     * @param mixed $imageSlugAttributeName : the attribute name on your model to make title image
     */
    protected function setUploadedImage($value)
    {
        $this->initAttributes();

        if ($value === null) {
            \Storage::disk('public')->delete($this->attributes[$this->imageAttributeName]);
            $this->attributes[$this->imageAttributeName] = '';
        }
        if (starts_with($value, 'data:image')) {
            $this->attributes[$this->imageAttributeName] = '';
            $this->tmpImage = \Image::make($value);
        }
    }

    /**
     * Fill image attribute with file path
     *
     * @param $path
     */
    public function fillImagePath($path)
    {
        $this->attributes[$this->imageAttributeName] = $path;
        // Reset tmpImage to prevent infinite loop
        $this->tmpImage = null;
    }

    public function getImageAttributeName()
    {
        if ($this->imageAttributeName === null) {
            $this->initAttributes();
        }

        return $this->imageAttributeName;
    }

    public function getImageSlugAttributeName()
    {
        if ($this->imageAttributeName === null) {
            $this->initAttributes();
        }

        return $this->imageSlugAttributeName;
    }

    public function getTmpImage()
    {
        return $this->tmpImage;
    }

    /**
     * Get model attributes name for image upload
     * Example : return ['nameAttribute' => 'image', slugAttribute' => 'title'];
     *
     * @return array
     */
    abstract function uploadableImage(): array;
}
