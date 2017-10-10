<?php

namespace Novius\Backpack\CRUD\Services;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UploadImageService
 * @package Novius\Backpack\CRUD\Services
 */
class UploadImageService extends AbstractUploadService
{
    /**
     * Allowed image extensions
     *
     * @var array
     */
    protected $allowed_extensions = ['gif', 'png', 'jpeg', 'jpg'];

    /**
     * Filled with images during model saving
     * Images will be used on Model "saved" event
     *
     * @var array
     */
    protected $tmpImages = [];

    /**
     * Set Model images attributes with good values
     *
     * @param Model $model
     * @return bool
     */
    public function fillImages(Model $model)
    {
        $this->initModel($model);
        foreach ($this->filesAttributes($this->model->uploadableImages()) as $imageAttribute) {
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
            // 1. Get image path
            $filePath = $this->getImagePath($imageAttributeName);

            // 2. Store the image on disk.
            \Storage::disk(self::STORAGE_DISK_NAME)->put($filePath, $image->stream());

            // 3. Save the path to the database
            $this->model->fillUploadedImageAttributeValue($imageAttributeName, $filePath);

            // 4. Performs custom actions on image after saving
            $imagePath = \Storage::disk(self::STORAGE_DISK_NAME)->getDriver()->getAdapter()->getPathPrefix().$filePath;
            $this->model->imagePathSaved($imagePath, $imageAttributeName, self::STORAGE_DISK_NAME);

            if (isset($this->tmpImages[$imageAttributeName])) {
                unset($this->tmpImages[$imageAttributeName]);
            }
        }

        return $this->model->save();
    }

    /**
     * Generate image path
     *
     * @param $imageAttributeName
     * @return string
     */
    protected function getImagePath(string $imageAttributeName): string
    {
        $folderName = snake_case(class_basename(get_class($this->model)));
        $destination_path = $folderName.'/'.$this->model->getKey().'/'.$imageAttributeName;
        $imageSlugAttribute = array_get($this->slugAttributes($this->model->uploadableImages()), $imageAttributeName);

        // 1. Generate a filename.
        $filename = md5(time()).'.jpg';
        if (!empty($imageSlugAttribute)) {
            $filename = str_slug($this->model->{$imageSlugAttribute}).'.jpg';
        }

        return $destination_path.'/'.$filename;
    }

    /**
     * Delete images on disk
     *
     * @param Model $model
     * @return bool
     */
    public function deleteImages(Model $model)
    {
        $this->initModel($model);
        foreach ($this->filesAttributes($this->model->uploadableImages()) as $imageAttribute) {
            $this->deleteImage($imageAttribute, $this->model->{$imageAttribute});
        }

        return true;
    }

    /**
     * Delete image file
     *
     * @param string $imageAttributeName
     * @param string $imageRelativePath
     * @return bool
     */
    protected function deleteImage(string $imageAttributeName, string $imageRelativePath): bool
    {
        // Delete image on disk
        \Storage::disk(self::STORAGE_DISK_NAME)->delete($imageRelativePath);

        // Performs custom actions after image deletion
        $imagePath = \Storage::disk(self::STORAGE_DISK_NAME)->getDriver()->getAdapter()->getPathPrefix().$imageRelativePath;
        $this->model->imagePathDeleted($imagePath, $imageAttributeName, self::STORAGE_DISK_NAME);

        return true;
    }

    /**
     * Delete old images
     *
     * @param $originalValue
     * @param string $imageAttributeName
     * @return bool
     */
    protected function deleteOldImage($originalValue, string $imageAttributeName)
    {
        // Delete old image :
        if (!empty($originalValue)) {
            $this->deleteImage($imageAttributeName, $originalValue);
        }
        // Set path to '' as there is no image in the input :
        $this->model->fillUploadedImageAttributeValue($imageAttributeName, '');

        return true;
    }

    /**
     * Upload new b64 image
     *
     * @param string $imageAttributeName
     * @param string $value
     * @param $originalValue
     * @return bool
     */
    protected function uploadNewImage(string $imageAttributeName, string $value, $originalValue)
    {
        // Upload a new image making it storable :
        $this->tmpImages[$imageAttributeName] = \Image::make($value);
        // Delete the old one :
        if (!empty($originalValue)) {
            $this->deleteImage($imageAttributeName, $originalValue);
        }
        $this->model->fillUploadedImageAttributeValue($imageAttributeName, '');

        return true;
    }

    /**
     * Set new image on disk
     *
     * @param string $value
     * @param string $originalValue
     * @param string $imageAttributeName
     * @return bool
     */
    protected function setNewImage(string $value, string $originalValue, string $imageAttributeName)
    {
        if ($value === $originalValue || starts_with($value, 'http')) {
            // If the image isn't in b64 or if the image have an absolute path (meaning it's an update) :
            $this->model->fillUploadedImageAttributeValue($imageAttributeName, $originalValue);
        } else {
            $this->model->fillUploadedImageAttributeValue($imageAttributeName, $value);
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
        $originalValue = null;

        // Is this image translatable (different image by locale)
        if ($this->isTranslatable($imageAttributeName)) {
            $locale = (string) request('locale', '');
            $value = $this->model->getTranslation($imageAttributeName, $locale);
            if (!empty($this->model->getOriginal($imageAttributeName))) {
                $originalValue = json_decode($this->model->getOriginal($imageAttributeName), true);
                $originalValue = array_get($originalValue, $locale, null);
            }
        } else {
            $value = $this->model->{$imageAttributeName};
            $originalValue = $this->model->getOriginal($imageAttributeName);
        }

        $path_parts = pathinfo($value);
        $path_extension = !empty($path_parts['extension']) ? $path_parts['extension'] : false;

        // Image is removed
        if (empty($value)) {
            $this->deleteOldImage($originalValue, $imageAttributeName);

            return;
        }

        // A new image is uploaded
        if (starts_with($value, 'data:image')) {
            $this->uploadNewImage($imageAttributeName, $value, $originalValue);

            return;
        }

        // An image is already uploaded, a new one is uploaded
        if (str_contains($path_extension, $this->allowed_extensions) && !empty($originalValue)) {
            $this->setNewImage($value, $originalValue, $imageAttributeName);

            return;
        }

        // No image is uploaded
        if (str_contains($path_extension, $this->allowed_extensions)) {
            $this->model->fillUploadedImageAttributeValue($imageAttributeName, '');

            return;
        }
    }
}
