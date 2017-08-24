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
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    private $events;

    /**
     * UploadableImageObserver constructor.
     * @param UploadImageService $slugService
     * @param Dispatcher $events
     */
    public function __construct(UploadImageService $imageUploadService, Dispatcher $events)
    {
        $this->imageUploadService = $imageUploadService;
        $this->events = $events;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return boolean
     */
    public function saved(Model $model)
    {
        return $this->imageUploadService->saveImage($model);
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
