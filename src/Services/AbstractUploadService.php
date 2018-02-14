<?php

namespace Novius\Backpack\CRUD\Services;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractUploadService
{
    const STORAGE_DISK_NAME = 'public'; // @TODO : make this option configurable

    /**
     * @var \Illuminate\Database\Eloquent\Model;
     */
    protected $model;

    /**
     * Get files attributes names
     *
     * @param array $uploableFiles
     * @return array
     */
    protected function filesAttributes(array $uploableFiles): array
    {
        if (empty($uploableFiles)) {
            return [];
        }

        if (array_get($uploableFiles, 0) === null) {
            $uploableFiles = [$uploableFiles];
        }

        return array_pluck($uploableFiles, 'name');
    }

    /**
     * Get slug attributes name (image attributes key based)
     *
     * @param array $uploableFiles
     * @return array
     */
    protected function slugAttributes(array $uploableFiles): array
    {
        if (array_get($uploableFiles, 0) === null) {
            $uploableFiles = [$uploableFiles];
        }

        return array_pluck($uploableFiles, 'slug', 'name');
    }

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
     * Check if attibute is translatable
     *
     * @param string $AttributeName
     * @return bool
     */
    protected function isTranslatable(string $AttributeName) : bool
    {
        if (method_exists($this->model, 'isTranslatableAttribute')) {
            return $this->model->isTranslatableAttribute($AttributeName);
        }

        return false;
    }
}
