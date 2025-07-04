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

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
