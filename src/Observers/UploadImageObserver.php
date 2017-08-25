<?php

namespace Novius\Backpack\CRUD\Observers;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Model;
use Novius\Backpack\CRUD\Services\UploadImageService;

class UploadImageObserver
{

    /**
     * @var \Cviebrock\EloquentSluggable\Services\SlugService
     */
    private $imageUploadService;

    /**
     * UploadableImageObserver constructor.
     * @param UploadImageService $slugService
     * @param Dispatcher $events
     */
    public function __construct(UploadImageService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return boolean
     */
    public function saving(Model $model)
    {
        return $this->imageUploadService->fillImages($model);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return boolean
     */
    public function saved(Model $model)
    {
        return $this->imageUploadService->saveImages($model);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return boolean
     */
    public function deleting(Model $model)
    {
        return $this->imageUploadService->deleteImage($model);
    }

}
