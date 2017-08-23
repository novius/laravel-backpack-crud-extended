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
     * Save image on disk and update Model column with image path
     *
     * @param \Intervention\Image\Image $image
     */
    public function saveImage(Model $model)
    {
        $this->initModel($model);

        $image = $model->getTmpImage();
        if (empty($image)) {
            return true;
        }

        $disk = 'public';
        $folderName = snake_case(class_basename(get_class($this->model)));
        $destination_path = $folderName.'/'.$this->model->getKey();

        // 1. Generate a filename.
        $filename = md5(time()).'.jpg';
        if (!empty($this->model->getImageSlugAttributeName()) && !empty($this->model->{$this->model->getImageSlugAttributeName()})) {
            $filename = str_slug($this->model->{$this->model->getImageSlugAttributeName()}).'.jpg';
        }

        // 2. Store the image on disk.
        \Storage::disk($disk)->put($destination_path.'/'.$filename, $image->stream());

        // 3. Save the path to the database
        $this->model->fillImagePath($destination_path.'/'.$filename);

        return $this->model->save();
    }

    public function deleteImage(Model $model)
    {
        $this->initModel($model);
        \Storage::disk('public')->delete($this->model->{$this->model->getImageAttributeName()});

        return true;
    }

}
