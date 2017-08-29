<?php

namespace Novius\Backpack\CRUD\Observers;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Model;
use Novius\Backpack\CRUD\Services\UploadFileService;
use Novius\Backpack\CRUD\Services\UploadImageService;

class UploadFileObserver
{
    /**
     * @var UploadFileService
     */
    private $uploadFileService;

    /**
     * UploadFileObserver constructor.
     * @param UploadFileService $uploadFileService
     */
    public function __construct(UploadFileService $uploadFileService)
    {
        $this->uploadFileService = $uploadFileService;
    }

    /**
     * @param Model $model
     */
    public function saving(Model $model)
    {
        $this->uploadFileService->fillFiles($model);
    }

    /**
     * @param Model $model
     */
    public function saved(Model $model)
    {
        $this->uploadFileService->saveFiles($model);
    }

    /**
     * @param Model $model
     */
    public function deleting(Model $model)
    {
        $this->uploadFileService->deleteFiles($model);
    }
}
