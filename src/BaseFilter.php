<?php

namespace AhmedAliraqi\LaravelFilterable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

abstract class BaseFilter
{
    /**
     * The Eloquent builder.
     *
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected Builder $builder;

    /**
     * Registered filters to operate upon.
     *
     * @var array
     */
    protected array $filters = [];

    /**
     * Create a new BaseFilters instance.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(
        protected Request $request
    ) {
    }

    /**
     * Apply the filters.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        foreach ($this->getFilters() as $filter) {
            $value = $this->request->query($filter);

            if (! $this->request->has($filter)) {
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