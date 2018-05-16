<?php

namespace Novius\Backpack\CRUD\ModelTraits\SpatieMediaLibrary;

use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait as HasMediaTraitNative;
use Spatie\MediaLibrary\Models\Media;

/**
 * Overrides the native HasMediaTrait trait to add some additional features :
 * - adds some methods to work with medias stored as attributes (for example with the UploadableImage trait)
 * - adds some properties and methods to easily handle conversions (crop and fit max)
 *
 * @package Novius\Backpack\CRUD\ModelTraits\SpatieMediaLibrary
 */
trait HasMediaTrait
{
    use HasMediaTraitNative;

    /**
     * Gets the attribute media URL with a fallback on the media stored on disk.
     *
     * @param string $attributeName
     * @param string $conversionName
     * @param bool $fallbackOnDisk Whether to fallback on the media stored on disk if the media collection doesn't exists
     * @return \Illuminate\Contracts\Routing\UrlGenerator|null|string
     */
    public function getAttributeMediaUrl(string $attributeName, string $conversionName = '', bool $fallbackOnDisk = true)
    {
        // Gets the URL from the media collection
        $collectionName = $this->getAttributeMediaCollectionName($attributeName);
        $url = $this->getFirstMediaUrl($collectionName, $conversionName);

        if (empty($url) && $fallbackOnDisk) {
            // Gets the URL from the media stored on disk as fallback
            $url = $this->getAttributeMediaUrlFromDisk($attributeName);
        }

        return url($url);
    }

    /**
     * Gets the attribute media URL with crop conversion and with a fallback on the media stored on disk.
     *
     * @param string $attributeName
     * @param string $cropConversionName
     * @param bool $fallbackOnDisk
     * @return \Illuminate\Contracts\Routing\UrlGenerator|null|string
     */
    public function getAttributeMediaCropUrl(string $attributeName, string $cropConversionName, bool $fallbackOnDisk = true)
    {
        return $this->getAttributeMediaUrl($attributeName, 'crop-'.$cropConversionName, $fallbackOnDisk);
    }

    /**
     * Gets the attribute media URL with fit max conversion and with a fallback on the media stored on disk.
     *
     * @param string $attributeName
     * @param string $fitMaxConversionName
     * @param bool $fallbackOnDisk
     * @return \Illuminate\Contracts\Routing\UrlGenerator|null|string
     */
    public function getAttributeMediaFitMaxUrl(string $attributeName, string $fitMaxConversionName, bool $fallbackOnDisk = true)
    {
        return $this->getAttributeMediaUrl($attributeName, 'fitmax-'.$fitMaxConversionName, $fallbackOnDisk);
    }

    /**
     * Gets the attribute media URL from disk.
     *
     * @param string $attributeName
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getAttributeMediaUrlFromDisk(string $attributeName)
    {
        return \Storage::disk('public')->url($this->getAttribute($attributeName));
    }

    /**
     * Gets the localized collection name of the attribute media.
     *
     * @param string $mediaAttributeName
     * @param string|null $locale
     * @return string
     */
    public function getAttributeMediaCollectionName(string $mediaAttributeName, string $locale = null)
    {
        $collectionName = $mediaAttributeName;

        // Appends the locale if translatable
        if ($this->isTranslatableAttributeMedia($mediaAttributeName)) {
            $collectionName .= '-'.($locale ?? $this->getLocale());
        }

        return $collectionName;
    }

    /**
     * Checks if the given attribute media is translatable.
     *
     * @param string $attributeName
     * @return bool
     */
    public function isTranslatableAttributeMedia(string $attributeName)
    {
        return method_exists($this, 'isTranslatableAttribute') && $this->isTranslatableAttribute($attributeName);
    }

    /**
     * Registers the crop conversions from the property "mediaConversionsCrop".
     *
     * @throws \Spatie\Image\Exceptions\InvalidManipulation
     */
    public function registerMediaConversionsCrop()
    {
        if (!property_exists($this, 'mediaConversionsCrop')) {
            return;
        }

        foreach ($this->mediaConversionsCrop as $collectionName => $conversions) {
            foreach ($conversions as $conversionName => $dimensions) {
                $this->addMediaConversion('crop-'.$conversionName)
                    ->crop(Manipulations::CROP_CENTER, $dimensions[0], $dimensions[1])
                    ->optimize()
                    ->performOnCollections($collectionName);
            }
        }
    }

    /**
     * Registers the fit max conversions from the property "mediaConversionsFitMax".
     *
     * @throws \Spatie\Image\Exceptions\InvalidManipulation
     */
    public function registerMediaConversionsFitMax()
    {
        if (!property_exists($this, 'mediaConversionsFitMax')) {
            return;
        }

        foreach ($this->mediaConversionsFitMax as $collectionName => $conversions) {
            foreach ($conversions as $conversionName => $dimensions) {
                $this->addMediaConversion('fitmax-'.$conversionName)
                    ->fit(Manipulations::FIT_MAX, $dimensions[0], $dimensions[1])
                    ->optimize()
                    ->performOnCollections($collectionName);
            }
        }
    }

    /**
     * Automatically registers the media conversions defined via properties.
     *
     * @param Media|null $media
     * @throws \Spatie\Image\Exceptions\InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null)
    {
        $this->registerMediaConversionsCrop();
        $this->registerMediaConversionsFitMax();
    }
}
