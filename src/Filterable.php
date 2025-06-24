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
    public function scopeFilter(Builder $query, ?BaseFilter $filters = null): Builder
    {
        if (! $filters  && property_exists($this, 'filter')) {
            $filters = App::make($this->filter);
        }

        return $filters->apply($query);
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
