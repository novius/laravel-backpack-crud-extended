<?php

namespace Novius\Backpack\CRUD\ModelTraits\SpatieTranslatable;

use Backpack\CRUD\ModelTraits\SpatieTranslatable\SluggableScopeHelpers as BackpackSluggableScopeHelpers;
use Illuminate\Database\Eloquent\Builder;

trait SluggableScopeHelpers
{
    use BackpackSluggableScopeHelpers;

    /**
     * Query scope for finding a model by its primary slug.
     *
     * @param \Illuminate\Database\Eloquent\Builder $scope
     * @param string $slug
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereSlug(Builder $scope, $slug)
    {
        return $scope->where($this->getSlugKeyName().'->'.$this->getLocale(), $slug);
    }
}
