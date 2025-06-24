<?php

namespace AhmedAliraqi\LaravelFilterable\Tests\Fixtures;

use AhmedAliraqi\LaravelFilterable\Filterable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Filterable;

    protected $guarded = [];

    public $timestamps = true;

    protected $filter = UserFilter::class;
}
