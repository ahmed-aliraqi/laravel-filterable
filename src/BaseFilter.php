<?php

namespace AhmedAliraqi\LaravelFilterable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
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
        protected Request $request
    ) {}

    /**
     * Apply the filters.
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
