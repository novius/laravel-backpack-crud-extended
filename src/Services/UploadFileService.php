<?php

namespace Novius\Backpack\CRUD\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

/**
 * Class UploadFileService
 * @package Novius\Backpack\CRUD\Services
 */
class UploadFileService extends AbstractUploadService
{
    /**
     * Filled with files during model saving
     * Images will be used on Model "saved" event
     *
     * @var array
     */
    protected $tmpFiles = [];

    /**
     * Set Model files attributes with good values
     *
     * @param Model $model
     * @return bool
     */
    public function fillFiles(Model $model)
    {
        $this->initModel($model);
        foreach ($this->filesAttributes($this->model->uploadableFiles()) as $fileAttribute) {
            if ($this->isTranslatable($fileAttribute)) {
                $this->setUploadedFileLang($fileAttribute);
            } else {
                $this->setUploadedFile($fileAttribute);
            }
        }

        return true;
    }

    /**
     * Save files on disk and update Model columns with files path
     *
     * @param Model $model
     * @return bool
     */
    public function saveFiles(Model $model)
    {
        $this->initModel($model);

        if (empty($this->tmpFiles)) {
            return true;
        }

        foreach ($this->tmpFiles as $fileAttributeName => $file) {
            // 1. Get image path
            $destinationPath = $this->getDestinationPath($fileAttributeName);
            $fileName = $this->getFilename($file, $fileAttributeName);

            // 2. Move the new file to the correct path
            $filePath = $file->storeAs($destinationPath, $fileName, self::STORAGE_DISK_NAME);

            // 3. Save the path to the database
            $this->model->fillUploadedFileAttributeValue($fileAttributeName, $filePath);

            if (isset($this->tmpFiles[$fileAttributeName])) {
                unset($this->tmpFiles[$fileAttributeName]);
            }
        }

        return $this->model->save();
    }

    protected function getFilename(UploadedFile $file, string $fileAttributeName): string
    {
        $slugAttributeName = array_get($this->slugAttributes($this->model->uploadableFiles()), $fileAttributeName);
        $filename = md5(time());
        if (!empty($slugAttributeName)) {
            $filename = str_slug($this->model->getAttribute($slugAttributeName));
        }
        $filename .= '.'.$file->getClientOriginalExtension();

        return $filename;
    }

    /**
     * @param string $fileAttributeName
     * @return string
     */
    protected function getDestinationPath(string $fileAttributeName): string
    {
        $folderName = snake_case(class_basename(get_class($this->model)));
        $destination_path = $folderName.'/'.$this->model->getKey().'/'.$fileAttributeName;

        return $destination_path;
    }

    /**
     * Delete file on disk
     *
     * @param Model $model
     * @return bool
     */
    public function deleteFiles(Model $model)
    {
        $this->initModel($model);
        foreach ($this->filesAttributes($this->model->uploadableFiles()) as $fileAttribute) {
            \Storage::disk(self::STORAGE_DISK_NAME)->delete($this->model->getAttribute($fileAttribute));
        }

        return true;
    }

    /**
     * Delete old file on disk
     *
     * @param $originalAttributeName
     * @param $fileAttributeName
     * @return bool
     */
    protected function deleteOldFile($originalAttributeName, $fileAttributeName)
    {
        \Storage::disk(self::STORAGE_DISK_NAME)->delete($originalAttributeName);
        $this->model->fillUploadedFileAttributeValue($fileAttributeName, '');

        return true;
    }

    /**
     * Generate new file on disk
     *
     * @param Request $request
     * @param $fileAttributeName
     * @return bool
     */
    protected function generateNewFile($request, $fileAttributeName)
    {
        $file = $request->file($fileAttributeName);
        $this->tmpFiles[$fileAttributeName] = $file;

        return true;
    }

    /**
     * Fill Model image attribute with good value
     *
     * @param string $fileAttributeName
     */
    protected function setUploadedFile(string $fileAttributeName)
    {
        /**
         * @var Request
         */
        $request = \Request::instance();
        $fileAttributeValue = $this->model->getAttribute($fileAttributeName);

        // If a new file is uploaded, delete old file from the disk
        if ($request->hasFile($fileAttributeName) && !empty($this->model->getOriginal($fileAttributeName)) && is_string($fileAttributeValue)) {
            $this->deleteOldFile($this->model->getOriginal($fileAttributeName), $fileAttributeName);
        }

        // if the file input is empty, delete the file from the disk
        if (!$request->hasFile($fileAttributeName) && $request->get($fileAttributeName) === null && !empty($this->model->getOriginal($fileAttributeName))) {
            $this->deleteOldFile($this->model->getOriginal($fileAttributeName), $fileAttributeName);
        }

        // if a new file is uploaded, store it on disk and its filename in the database
        if ($request->hasFile($fileAttributeName) && $request->file($fileAttributeName)->isValid() && is_a($fileAttributeValue, UploadedFile::class)) {
            $this->generateNewFile($request, $fileAttributeName);
        }
    }

    /**
     * Fill Model image attribute with good value
     *
     * @param string $fileAttributeName
     */
    protected function setUploadedFileLang(string $fileAttributeName)
    {
        /**
         * @var Request
         */
        $request = \Request::instance();
        $locale = (string) request('locale', '');

        $fileAttributeValue = $this->model->getTranslation($fileAttributeName, $locale);

        $originalLocale = null;

        if (!empty($this->model->getOriginal($fileAttributeName))) {
            $original = json_decode($this->model->getOriginal($fileAttributeName), true);
            $originalLocale = array_get($original, $locale, null);
        }

        // If a new file is uploaded, delete old file from the disk
        if ($request->hasFile($fileAttributeName) && !empty($originalLocale) && is_array($fileAttributeValue)) {
            $this->deleteOldFile($originalLocale, $fileAttributeName);
        }

        // if the file input is empty, delete the file from the disk
        if (!$request->hasFile($fileAttributeName) && $request->get($fileAttributeName) === null && !empty($originalLocale)) {
            $this->deleteOldFile($originalLocale, $fileAttributeName);
        }

        // if a new file is uploaded, store it on disk and its filename in the database
        if ($request->hasFile($fileAttributeName) && $request->file($fileAttributeName)->isValid() && is_array($fileAttributeValue)) {
            $this->generateNewFile($request, $fileAttributeName);
        }
    }
}
