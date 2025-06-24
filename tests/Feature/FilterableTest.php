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
        $filter = new UserFilter(['name' => 'John']);

        $users = User::filter($filter)->get();

        $this->assertCount(1, $users);
        $this->assertEquals('John Doe', $users->first()->name);
    }

    public function test_it_can_filter_by_exact_email()
    {
        $filter = new UserFilter(['email' => 'jane@example.com']);

        $users = User::filter($filter)->get();

        $this->assertCount(1, $users);
        $this->assertEquals('jane@example.com', $users->first()->email);
    }

    public function test_it_can_filter_by_age_range()
    {
        $filter = new UserFilter(['age_from' => 25, 'age_to' => 30]);

        $users = User::filter($filter)->get();

        $this->assertCount(2, $users);
        $this->assertEquals([25, 30], $users->pluck('age')->toArray());
    }

    public function test_it_returns_all_records_when_no_filters_applied()
    {
        $filter = new UserFilter([]);

        $users = User::filter($filter)->get();

        $this->assertCount(3, $users);
    }

    public function test_it_can_combine_multiple_filters()
    {
        $filter = new UserFilter([
            'age_from' => 30,
            'name' => 'Bob',
        ]);

        $users = User::filter($filter)->get();

        $this->assertCount(1, $users);
        $this->assertEquals('Bob Johnson', $users->first()->name);
        $this->assertEquals(35, $users->first()->age);
    }

    public function test_it_can_filter_from_request_query_parameters()
    {
        $this->instance('request', new Request([
            'age_from' => 30,
            'name' => 'Bob',
        ]));

        $filter = new UserFilter([]);

        $users = User::filter($filter)->get();

        $this->assertCount(1, $users);
        $this->assertEquals('Bob Johnson', $users->first()->name);
        $this->assertEquals(35, $users->first()->age);

        $this->instance('request', new Request([
            'age_from' => 36,
            'name' => 'Bob',
        ]));

        $filter = new UserFilter([]);

        $users = User::filter($filter)->get();

        $this->assertCount(0, $users);
    }

    public function test_it_ignores_undefined_filters()
    {
        $filter = new UserFilter([
            'undefined_filter' => 'value',
            'name' => 'John',
        ]);

        $users = User::filter($filter)->get();

        $this->assertCount(1, $users);
        $this->assertEquals('John Doe', $users->first()->name);
    }

    public function test_it_can_include_relations()
    {
        $user = User::first();
        $user->posts()->create([
            'title' => 'Test Post',
            'content' => 'Test Content',
        ]);

        $filter = new UserFilter([
            'name' => 'John',
            'include' => 'posts',
        ]);

        $users = User::filter($filter)->get();

        $this->assertCount(1, $users);
        $this->assertTrue($users->first()->relationLoaded('posts'));
        $this->assertCount(1, $users->first()->posts);
        $this->assertEquals('Test Post', $users->first()->posts->first()->title);
    }

    public function test_it_can_include_multiple_relations()
    {
        $user = User::first();
        $post = $user->posts()->create([
            'title' => 'Test Post',
            'content' => 'Test Content',
        ]);

        $user->comments()->create([
            'post_id' => $post->id,
            'content' => 'Direct Comment',
        ]);

        $filter = new UserFilter([
            'name' => 'John',
            'include' => 'posts,comments',
        ]);

        $users = User::filter($filter)->get();

        $this->assertCount(1, $users);
        $this->assertTrue($users->first()->relationLoaded('posts'));
        $this->assertTrue($users->first()->relationLoaded('comments'));
        $this->assertCount(1, $users->first()->posts);
        $this->assertCount(1, $users->first()->comments);
    }

    public function test_it_can_include_nested_relations()
    {
        $user = User::first();
        $post = $user->posts()->create([
            'title' => 'Test Post',
            'content' => 'Test Content',
        ]);

        $post->comments()->create([
            'user_id' => $user->id,
            'content' => 'Nested Comment',
        ]);

        $filter = new UserFilter([
            'name' => 'John',
            'include' => 'posts.comments',
        ]);

        $users = User::filter($filter)->get();

        $this->assertCount(1, $users);
        $this->assertTrue($users->first()->relationLoaded('posts'));
        $this->assertTrue($users->first()->posts->first()->relationLoaded('comments'));
        $this->assertCount(1, $users->first()->posts->first()->comments);
        $this->assertEquals('Nested Comment', $users->first()->posts->first()->comments->first()->content);
    }

    public function test_it_can_load_includes_on_model_instance()
    {
        $user = User::first();
        $user->posts()->create([
            'title' => 'Test Post',
            'content' => 'Test Content',
        ]);

        $this->assertFalse($user->relationLoaded('posts'));

        $filter = new UserFilter(['include' => 'posts']);
        $user->loadIncludes($filter);

        $this->assertTrue($user->relationLoaded('posts'));
        $this->assertCount(1, $user->posts);
    }

    public function test_it_can_load_multiple_includes_on_model_instance()
    {
        $user = User::first();
        $post = $user->posts()->create([
            'title' => 'Test Post',
            'content' => 'Test Content',
        ]);

        $user->comments()->create([
            'post_id' => $post->id,
            'content' => 'Test Comment',
        ]);

        $this->assertFalse($user->relationLoaded('posts'));
        $this->assertFalse($user->relationLoaded('comments'));

        $filter = new UserFilter(['include' => 'posts,comments']);
        $user->loadIncludes($filter);

        $this->assertTrue($user->relationLoaded('posts'));
        $this->assertTrue($user->relationLoaded('comments'));
        $this->assertCount(1, $user->posts);
        $this->assertCount(1, $user->comments);
    }

    public function test_it_can_load_nested_includes_on_model_instance()
    {
        $user = User::first();
        $post = $user->posts()->create([
            'title' => 'Test Post',
            'content' => 'Test Content',
        ]);

        $post->comments()->create([
            'user_id' => $user->id,
            'content' => 'Test Comment',
        ]);

        $this->assertFalse($user->relationLoaded('posts'));

        $filter = new UserFilter(['include' => 'posts.comments']);
        $user->loadIncludes($filter);

        $this->assertTrue($user->relationLoaded('posts'));
        $this->assertTrue($user->posts->first()->relationLoaded('comments'));
        $this->assertCount(1, $user->posts->first()->comments);
    }

    public function test_it_uses_default_filter_class_when_no_filter_provided()
    {
        $user = User::first();
        $user->posts()->create([
            'title' => 'Test Post',
            'content' => 'Test Content',
        ]);

        $this->assertFalse($user->relationLoaded('posts'));

        // Use the Request facade to simulate query parameters
        $this->instance('request', new Request(['include' => 'posts']));

        $user->loadIncludes();

        $this->assertTrue($user->relationLoaded('posts'));
        $this->assertCount(1, $user->posts);
    }
}
