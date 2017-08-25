<?php

namespace Novius\Backpack\CRUD\Services;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SlugService
 *
 * @package Cviebrock\EloquentSluggable\Services
 */
class UploadImageService
{
    /**
     * Filled with images during model saving
     * Images will be used on Model "saved" event
     *
     * @var array
     */
    protected $tmpImages = [];

    /**
     * @var \Illuminate\Database\Eloquent\Model;
     */
    protected $model;

    /**
     * Init the service Model
     *
     * @param Model $model
     */
    protected function initModel(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Set Model images attributes with good values
     *
     * @param Model $model
     * @return bool
     */
    public function fillImages(Model $model)
    {
        $this->initModel($model);
        foreach ($this->imagesAttributes() as $imageAttribute) {
            $this->setUploadedImage($imageAttribute);
        }

        return true;
    }

    /**
     * Save images on disk and update Model columns with images path
     *
     * @param Model $model
     * @return bool
     */
    public function saveImages(Model $model)
    {
        $this->initModel($model);

        if (empty($this->tmpImages)) {
            return true;
        }

        foreach ($this->tmpImages as $imageAttributeName => $image) {
            $disk = 'public';
            $folderName = snake_case(class_basename(get_class($this->model)));
            $destination_path = $folderName.'/'.$this->model->getKey().'/'.$imageAttributeName;
            $imageSlugAttribute = array_get($this->slugAttributes(), $imageAttributeName);

            // 1. Generate a filename.
            $filename = md5(time()).'.jpg';
            if (!empty($imageSlugAttribute)) {
                $filename = str_slug($this->model->{$imageSlugAttribute}).'.jpg';
            }

            // 2. Store the image on disk.
            \Storage::disk($disk)->put($destination_path.'/'.$filename, $image->stream());

            // 3. Save the path to the database
            $this->model->fillImageUploadedAttributeValue($imageAttributeName, $destination_path.'/'.$filename);

            if (isset($this->tmpImages[$imageAttributeName])) {
                unset($this->tmpImages[$imageAttributeName]);
            }
        }

        return $this->model->save();
    }

    /**
     * Delete images on disk
     *
     * @param Model $model
     * @return bool
     */
    public function deleteImage(Model $model)
    {
        $this->initModel($model);
        foreach ($this->imagesAttributes() as $imageAttribute) {
            \Storage::disk('public')->delete($this->model->{$imageAttribute});
        }

        return true;
    }

    /**
     * Fill Model image attribute with good value
     *
     * @param string $imageAttributeName
     */
    protected function setUploadedImage(string $imageAttributeName)
    {
        $value = $this->model->{$imageAttributeName};

        if (empty($value)) {
            // Delete old image
            if (!empty($this->model->getOriginal($imageAttributeName))) {
                \Storage::disk('public')->delete($this->model->getOriginal($imageAttributeName));
            }
            $this->model->fillImageUploadedAttributeValue($imageAttributeName, '');

            return;
        }

        if (starts_with($value, 'data:image')) {
            // Upload a new image
            $this->tmpImages[$imageAttributeName] = \Image::make($value);
            if (empty($this->model->getOriginal($imageAttributeName))) {
                // No image before
                $this->model->fillImageUploadedAttributeValue($imageAttributeName, '');
            } else {
                // Erase existing image
                $this->model->fillImageUploadedAttributeValue($imageAttributeName, $this->model->getOriginal($imageAttributeName));
            }

            return;
        }

        if (ends_with($value, '.jpg') && !empty($this->model->getOriginal($imageAttributeName))) {
            // Keep same image
            $this->model->fillImageUploadedAttributeValue($imageAttributeName, $this->model->getOriginal($imageAttributeName));

            return;
        }

        if (!ends_with($value, '.jpg')) {
            // No image uploaded
            $this->model->fillImageUploadedAttributeValue($imageAttributeName, '');
        }
    }

    /**
     * Get image attributes names
     *
     * @return array
     */
    protected function imagesAttributes() : array
    {
        $uploableImages = $this->model->uploadableImages();
        if (array_get($uploableImages, 0) === null) {
            $uploableImages = [$uploableImages];
        }

        return array_pluck($uploableImages, 'name');
    }

    /**
     * Get slug attributes name (image attributes key based)
     *
     * @return array
     */
    protected function slugAttributes() : array
    {
        $uploableImages = $this->model->uploadableImages();
        if (array_get($uploableImages, 0) === null) {
            $uploableImages = [$uploableImages];
        }

        return array_pluck($uploableImages, 'slug', 'name');
    }
}
