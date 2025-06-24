<?php

namespace AhmedAliraqi\LaravelFilterable\Tests\Feature;

use AhmedAliraqi\LaravelFilterable\Tests\TestCase;
use Illuminate\Support\Facades\File;

class MakeFilterCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Clean up the test directories
        $this->cleanDirectory($this->app->path('Http/Filters'));
        $this->cleanDirectory($this->app->path('Filters'));
    }

    protected function tearDown(): void
    {
        // Clean up after tests
        $this->cleanDirectory($this->app->path('Http/Filters'));
        $this->cleanDirectory($this->app->path('Filters'));

        parent::tearDown();
    }

    protected function cleanDirectory($path)
    {
        if (File::isDirectory($path)) {
            File::deleteDirectory($path);
        }
    }

    public function test_it_can_create_filter_class()
    {
        $filterPath = $this->app->path('Http/Filters/UserFilter.php');

        $this->artisan('make:filter UserFilter')
            ->assertSuccessful();

        $this->assertFileExists($filterPath);

        $content = File::get($filterPath);
        $this->assertStringContainsString('namespace App\Http\Filters;', $content);
        $this->assertStringContainsString('class UserFilter extends BaseFilter', $content);
        $this->assertStringContainsString('protected array $filters = [', $content);
    }

    public function test_it_can_create_filter_class_in_custom_namespace()
    {
        // Ensure the directory exists
        $basePath = $this->app->basePath();
        $filtersPath = $this->app->path('Filters');

        if (! File::exists($filtersPath)) {
            File::makeDirectory($filtersPath, 0755, true);
        }

        $this->artisan('make:filter', [
            'name' => 'Filters/CustomFilter',
        ])->assertSuccessful();

        $filterPath = $filtersPath.'/CustomFilter.php';
        $this->assertTrue(File::exists($filterPath), "Filter file was not created at expected path: $filterPath");

        $content = File::get($filterPath);
        $this->assertStringContainsString('namespace App\Filters;', $content);
        $this->assertStringContainsString('class CustomFilter extends BaseFilter', $content);
    }

    public function test_it_wont_overwrite_existing_filter()
    {
        $filterPath = $this->app->path('Http/Filters/UserFilter.php');

        // Create the filter first time
        $this->artisan('make:filter UserFilter')
            ->assertSuccessful();

        $originalContent = File::get($filterPath);

        // Try to create it again
        $this->artisan('make:filter UserFilter')
            ->assertSuccessful();

        // Content should remain unchanged
        $this->assertEquals($originalContent, File::get($filterPath));
    }
}
