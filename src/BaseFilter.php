<?php

namespace AhmedAliraqi\LaravelFilterable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

abstract class BaseFilter
{
    /**
     * The Eloquent builder.
     */
    protected Builder $builder;

    /**
     * Registered filters to operate upon.
     */
    protected array $filters = [];

    /**
     * Create a new BaseFilters instance.
     */
    public function __construct(
        protected array $data = []
    ) {
        if (empty($this->data)) {
            $this->data = App::make('request')->query();
        }
    }

    protected function getFilteredData(): array
    {
        return Arr::only($this->data, $this->filters);
    }

    /**
     * Apply the filters.
     */
    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        foreach ($this->getFilters() as $filter) {
            $value = data_get($this->data, $filter);

            if (! array_key_exists($filter, $this->getFilteredData())) {
                continue;
            }

            $methodName = Str::camel($filter);

            if (method_exists($this, $methodName)) {
                $this->$methodName($value);
            }
        }

        return $this->builder;
    }

    public function getFilters(): array
    {
        return property_exists($this, 'filters') ? $this->filters : [];
    }
}
