<?php

namespace AhmedAliraqi\LaravelFilterable\Tests\Fixtures;

use AhmedAliraqi\LaravelFilterable\BaseFilter;

class UserFilter extends BaseFilter
{
    protected array $filters = ['name', 'email', 'age', 'age_from', 'age_to'];

    public function name($value)
    {
        $this->builder->where('name', 'like', "$value%");
    }

    public function email($value)
    {
        $this->builder->where('email', $value);
    }

    public function age($value)
    {
        $this->builder->where('age', $value);
    }

    public function ageFrom($value)
    {
        $this->builder->where('age', '>=', $value);
    }

    public function ageTo($value)
    {
        $this->builder->where('age', '<=', $value);
    }
}
