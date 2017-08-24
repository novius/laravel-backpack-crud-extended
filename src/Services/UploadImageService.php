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
     * @var \Illuminate\Database\Eloquent\Model;
     */
    protected $model;

    protected function initModel($model)
    {
        $this->model = $model;
    }

    /**
     * Set model images attributes with good values
     * Stock images uploaded on
     *
     * @param Model $model
     * @return bool
     */
    public function fillImages(Model $model)
    {
        $this->initModel($model);
        foreach ($this->model->imagesAttributes() as $imageAttribute) {
            $this->model->setUploadedImage($this->model->{$imageAttribute}, $imageAttribute);
        }

        return true;
    }

    /**
     * Save image on disk and update Model column with image path
     *
     * @param \Intervention\Image\Image $image
     */
    public function saveImages(Model $model)
    {
        $this->initModel($model);

        $images = $model->getTmpImages();
        if (empty($images)) {
            return true;
        }

        foreach ($images as $attributeImageName => $image) {
            $disk = 'public';
            $folderName = snake_case(class_basename(get_class($this->model)));
            $destination_path = $folderName.'/'.$this->model->getKey().'/'.$attributeImageName;
            $imageSlugAttribute = array_get($model->slugAttributes(), $attributeImageName);

            // 1. Generate a filename.
            $filename = md5(time()).'.jpg';
            if (!empty($imageSlugAttribute)) {
                $filename = str_slug($this->model->{$imageSlugAttribute}).'.jpg';
            }

            // 2. Store the image on disk.
            \Storage::disk($disk)->put($destination_path.'/'.$filename, $image->stream());

            // 3. Save the path to the database
            $this->model->fillImagePath($destination_path.'/'.$filename, $attributeImageName);
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
        foreach ($this->model->imagesAttributes() as $imageAttribute) {
            \Storage::disk('public')->delete($this->model->{$imageAttribute});
        }

        return true;
    }
}
