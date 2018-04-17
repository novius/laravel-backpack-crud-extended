<?php

namespace Novius\Backpack\CRUD\ModelTraits\SpatieMediaLibrary;

use Novius\Backpack\CRUD\ModelTraits\UploadableImage as UploadableImageOriginal;

/**
 * Trait UploadableImage
 *
 * To make media upload work :
 * - implement the trait Novius\Backpack\CRUD\ModelTraits\SpatieMediaLibrary\HasMediaTrait on your model
 * - call $this->setUploadedImage($value) in your model attribute mutator
 *
 * @package Novius\Backpack\CRUD\ModelTraits\SpatieMediaLibrary
 */
trait UploadableImage
{
    use UploadableImageOriginal {
        imagePathSaved as imagePathSavedOriginal;
        imagePathDeleted as imagePathDeletedOriginal;
    }

    /**
     * Callback triggered after image saved on disk
     *
     * @param string $imageAttributeName
     * @param string|null $imagePath
     * @param string|null $diskName
     * @return bool
     */
    public function imagePathSaved(string $imagePath, string $imageAttributeName = null, string $diskName = null) : bool
    {
        // Adds the image to the medialibrary
        $this->addMedia($imagePath)
            ->preservingOriginal()
            ->toMediaCollection($this->getImageCollectionName($imageAttributeName));

        return $this->imagePathSavedOriginal($imagePath, $imageAttributeName, $diskName);
    }

    /**
     * Callback triggered after image deleted on disk
     *
     * @param string $imagePath
     * @param string|null $imageAttributeName
     * @param string|null $diskName
     * @return bool
     */
    public function imagePathDeleted(string $imagePath, string $imageAttributeName = null, string $diskName = null) : bool
    {
        // Removes the image from the medialibrary
        $this->clearMediaCollection($this->getImageCollectionName($imageAttributeName));

        return $this->imagePathDeletedOriginal($imagePath, $imageAttributeName, $diskName);
    }

    /**
     * Gets the localized image attribute name
     *
     * @param string $imageAttributeName
     * @param string|null $locale
     * @return string
     */
    public function getImageCollectionName(string $imageAttributeName, string $locale = null)
    {
        $collectionName = $imageAttributeName;

        // Appends the locale if translatable
        if ($this->isTranslatableImageAttribute($imageAttributeName)) {
            $collectionName .= '-'.($locale ?? $this->getLocale());
        }

        return $collectionName;
    }
}
