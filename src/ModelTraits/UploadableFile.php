<?php

namespace Novius\Backpack\CRUD\ModelTraits;

use Novius\Backpack\CRUD\Observers\UploadFileObserver;
use Novius\Backpack\CRUD\Services\UploadFileService;

/**
 * Trait UploadableFile
 *
 * To make documents upload works, just call $this->setUploadedFile($value) on your model attribute mutator
 *
 * @package Novius\Backpack\CRUD\ModelTraits
 */
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
     * Put value on desired model document attribute
     *
     * @param string $fileAttributeName
     * @param string $path
     */
    public function fillUploadedFileAttributeValue(string $fileAttributeName, string $path)
    {
        if (method_exists($this, 'isTranslatableAttribute')
            && $this->isTranslatableAttribute($fileAttributeName)
        ) {
            //
            $this->setTranslation($fileAttributeName, (string) request('locale', $this->getLocale()), $path); // Default value is relevant when using seeders or any environment where we dont have acces to "request".
        } else {
            $this->setAttribute($fileAttributeName, $path);
        }
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
