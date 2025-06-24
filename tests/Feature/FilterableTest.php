<?php

namespace AhmedAliraqi\LaravelFilterable\Tests\Feature;

use AhmedAliraqi\LaravelFilterable\Tests\Fixtures\User;
use AhmedAliraqi\LaravelFilterable\Tests\Fixtures\UserFilter;
use AhmedAliraqi\LaravelFilterable\Tests\TestCase;
use Illuminate\Http\Request;

class FilterableTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Create test users
        User::create(['name' => 'John Doe', 'email' => 'john@example.com', 'age' => 25]);
        User::create(['name' => 'Jane Smith', 'email' => 'jane@example.com', 'age' => 30]);
        User::create(['name' => 'Bob Johnson', 'email' => 'bob@example.com', 'age' => 35]);
    }

    public function test_it_can_filter_by_name()
    {
        $request = Request::create('/', 'GET', ['name' => 'John']);
        $filter = new UserFilter($request);

        $users = User::filter($filter)->get();

        $this->assertCount(1, $users);
        $this->assertEquals('John Doe', $users->first()->name);
    }

    public function test_it_can_filter_by_exact_email()
    {
        $request = Request::create('/', 'GET', ['email' => 'jane@example.com']);
        $filter = new UserFilter($request);

        $users = User::filter($filter)->get();

        $this->assertCount(1, $users);
        $this->assertEquals('jane@example.com', $users->first()->email);
    }

    public function test_it_can_filter_by_age_range()
    {
        $request = Request::create('/', 'GET', ['age_from' => 25, 'age_to' => 30]);
        $filter = new UserFilter($request);

        $users = User::filter($filter)->get();

        $this->assertCount(2, $users);
        $this->assertEquals([25, 30], $users->pluck('age')->toArray());
    }

    public function test_it_returns_all_records_when_no_filters_applied()
    {
        $request = Request::create('/', 'GET', []);
        $filter = new UserFilter($request);

        $users = User::filter($filter)->get();

        $this->assertCount(3, $users);
    }

    public function test_it_can_combine_multiple_filters()
    {
        $request = Request::create('/', 'GET', [
            'age_from' => 30,
            'name' => 'Bob',
        ]);
        $filter = new UserFilter($request);

        $users = User::filter($filter)->get();

        $this->assertCount(1, $users);
        $this->assertEquals('Bob Johnson', $users->first()->name);
        $this->assertEquals(35, $users->first()->age);
    }

    public function test_it_ignores_undefined_filters()
    {
        $request = Request::create('/', 'GET', [
            'undefined_filter' => 'value',
            'name' => 'John',
        ]);
        $filter = new UserFilter($request);

        $users = User::filter($filter)->get();

        $this->assertCount(1, $users);
        $this->assertEquals('John Doe', $users->first()->name);
    }
}
