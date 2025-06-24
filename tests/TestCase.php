<?php

namespace AhmedAliraqi\LaravelFilterable\Tests;

use AhmedAliraqi\LaravelFilterable\FilterableServiceProvider;
use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected $appPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->appPath = __DIR__.'/laravel/app';

        // Set custom app path for testing
        $this->app->setBasePath($this->appPath);

        // Set up test application structure
        $this->setUpTestEnvironment();

        // Create the users table
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        // Create necessary directories
        $this->ensureDirectoryExists($this->app->path());
        $this->ensureDirectoryExists($this->app->path('Http/Filters'));
        $this->ensureDirectoryExists($this->app->path('Filters'));
    }

    protected function setUpTestEnvironment()
    {
        $basePath = __DIR__.'/laravel';
        $this->app->setBasePath($basePath);

        // Ensure test application structure exists
        $this->ensureDirectoryExists($basePath);
        $this->ensureDirectoryExists($this->app->path());
        $this->ensureDirectoryExists($this->app->path('Http/Filters'));
        $this->ensureDirectoryExists($this->app->path('Filters'));

        // Create composer.json if it doesn't exist
        if (! File::exists($basePath.'/composer.json')) {
            File::put($basePath.'/composer.json', json_encode([
                'name' => 'test/app',
                'autoload' => [
                    'psr-4' => [
                        'App\\' => 'app/',
                    ],
                ],
            ], JSON_PRETTY_PRINT));
        }
    }

    protected function getPackageProviders($app)
    {
        return [
            FilterableServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

    protected function getEnvironmentSetUp($app)
    {
        // Set base path for the test application
        $app->setBasePath($this->appPath ?? __DIR__.'/laravel');

        // Use sqlite in memory for testing
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function ensureDirectoryExists($path)
    {
        if (! is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }
}
