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
     * The list of relations that are allowed to be included with the query.
     */
    protected array $supportedInclude = [];

    /**
     * Create a new BaseFilters instance.
     */
    public function __construct(
        protected array $data = [],
        protected array $include = [],
    ) {
        $request = App::make('request');

        if (empty($this->data)) {
            $this->data = $request->query();
        }
    }

    /**
     * Apply the filters.
     */
    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        foreach ($this->filters as $filter) {
            $value = data_get($this->data, $filter);

            if (! array_key_exists($filter, $this->getFilters())) {
                continue;
            }

            $methodName = Str::camel($filter);

            if (method_exists($this, $methodName)) {
                $this->$methodName($value);
            }
        }

        foreach ($this->getInclude() as $include) {
            $this->builder->with($include);
        }

        return $this->builder;
    }

    public function getFilters(): array
    {
        return Arr::only($this->data, $this->filters);
    }

    public function getInclude(): array
    {
        $include = explode(',', data_get($this->data, 'include'));

        return array_values(
            array_filter(
                $include,
                fn ($value) => in_array($value, $this->supportedInclude)
            )
        );
    }
}
