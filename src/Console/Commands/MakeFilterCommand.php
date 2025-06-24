<?php

namespace AhmedAliraqi\LaravelFilterable\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class MakeFilterCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:filter';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Filter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Eloquent filter class';

    protected function getStub()
    {
        return __DIR__.'/stubs/filter.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        $name = $this->argument('name');

        // Handle path-style namespaces (e.g., "Filters/CustomFilter")
        if (Str::contains($name, '/')) {
            $segments = explode('/', $name);
            $className = array_pop($segments);

            return $rootNamespace.'\\'.implode('\\', $segments);
        }

        return $rootNamespace.'\Http\Filters';
    }

    protected function getNameInput()
    {
        $name = parent::getNameInput();

        // Extract just the class name if a path is provided
        if (Str::contains($name, '/')) {
            return Str::afterLast($name, '/');
        }

        return $name;
    }
}
