<?php

namespace AhmedAliraqi\LaravelFilterable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|$this filter(?BaseFilter $filters = null)
 */
trait Filterable
{
    /**
     * Apply all relevant thread filters.
     */
    public function scopeFilter(Builder $query, ?BaseFilter $filter = null): Builder
    {
        if (! $filter && property_exists($this, 'filter')) {
            $filter = App::make($this->filter);
        }

        return $filter->apply($query);
    }

    /**
     * Eager load relationships specified in the filter's include parameter.
     * Relations can be specified as comma-separated values ('posts,comments')
     * or using dot notation for nested relations ('posts.comments').
     */
    public function loadIncludes(?BaseFilter $filter = null): self
    {
        if (! $filter && property_exists($this, 'filter')) {
            $filter = App::make($this->filter);
        }

        foreach ($filter->getInclude() as $relation) {
            $this->load($relation);
        }

        return $this;
    }

    /**
     * Get the number of models to return per page.
     *
     * @return int
     */
    public function getPerPage()
    {
        return Request::query('perPage', parent::getPerPage());
    }
}
