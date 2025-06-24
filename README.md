# Laravel Filterable

A simple and elegant way to add filtering capabilities to your Laravel Eloquent models.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ahmed-aliraqi/laravel-filterable.svg?style=flat-square)](https://packagist.org/packages/ahmed-aliraqi/laravel-filterable)

[![tests](https://github.com/ahmed-aliraqi/laravel-filterable/actions/workflows/tests.yaml/badge.svg?branch=main)](https://github.com/ahmed-aliraqi/laravel-filterable/actions/workflows/tests.yaml)

[![Total Downloads](https://img.shields.io/packagist/dt/ahmed-aliraqi/laravel-filterable.svg?style=flat-square)](https://packagist.org/packages/ahmed-aliraqi/laravel-filterable)

## Installation

You can install the package via composer:

```bash
composer require ahmed-aliraqi/laravel-filterable
```

## Usage

### 1. Create a Filter Class

Use the provided artisan command to create a new filter class:

```bash
php artisan make:filter UserFilter
```

This will create a new filter class in `app/Http/Filters`:

```php
namespace App\Http\Filters;

use AhmedAliraqi\LaravelFilterable\BaseFilter;

class UserFilter extends BaseFilter
{
    protected array $filters = [
        'name',
        'email',
        'age'
    ];

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
}
```

### 2. Apply the Trait to Your Model

Add the `Filterable` trait to your model:

```php
use AhmedAliraqi\LaravelFilterable\Filterable;

class User extends Model
{
    use Filterable;

    // Optionally specify the filter class
    protected $filter = UserFilter::class;
}
```

### 3. Use the Filter

You can now filter your model queries:

```php
// In your controller
use App\Http\Filters\UserFilter;

public function index(Request $request)
{
    $users = User::filter(new UserFilter($request))->get();

    return response()->json($users);
}
```

Or if you've specified the filter class in your model:

```php
$users = User::filter()->get();
```

### Query Parameters

Your API endpoints will now automatically respond to query parameters that match your defined filters:

```
/api/users?name=John&age=25
```

## Advanced Usage

### Custom Namespaces

You can create filters in custom namespaces:

```bash
php artisan make:filter Filters/CustomFilter
```

### Complex Filters

You can define complex filter methods:

```php
class UserFilter extends BaseFilter
{
    protected array $filters = [
        'age_range',
        'created_between'
    ];

    public function ageRange($value)
    {
        [$min, $max] = explode(',', $value);
        $this->builder->whereBetween('age', [$min, $max]);
    }

    public function createdBetween($value)
    {
        [$start, $end] = explode(',', $value);
        $this->builder->whereBetween('created_at', [$start, $end]);
    }
}
```

### Filter Arrays

You can handle array inputs in your filters:

```php
class UserFilter extends BaseFilter
{
    protected array $filters = ['status'];

    public function status(array $value)
    {
        $this->builder->whereIn('status', $value);
    }
}
```

## Testing

```bash
composer test
```

## Authors

- [Ahmed Fathy](https://github.com/ahmed-aliraqi)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
