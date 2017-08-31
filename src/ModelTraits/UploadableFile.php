<?php

namespace Novius\Backpack\CRUD\ModelTraits;

use Novius\Backpack\CRUD\Observers\UploadFileObserver;
use Novius\Backpack\CRUD\Services\UploadFileService;

trait UploadableFile
{
    /**
     * Hook into the Eloquent model events to upload or delete elements
     */
    public static function bootUploadableFile()
    {
        // We need to create a shared instance of Observer (only on this model) to keep some information at each fired events
        $observer = new UploadFileObserver(new UploadFileService());
        app()->instance(UploadFileObserver::class, $observer);

        // Observe Model Events with same instance of observer
        static::observe(app(UploadFileObserver::class));
    }

    /**
     * Put value on desired model image attribute
     *
     * @param string $fileAttributeName
     * @param string $path
     */
    public function fillUploadedFileAttributeValue(string $fileAttributeName, string $path)
    {
        $this->{$fileAttributeName} = $path;
    }

    /**
     * Get model attributes name for files upload
     * Simple example: return ['name' => 'document', slug' => 'title'];
     * With multiple files: return [['name' => 'document', slug' => 'title'], ['name' => 'image']];
     *
     * @return array
     */
    abstract public function uploadableFiles(): array;
}
