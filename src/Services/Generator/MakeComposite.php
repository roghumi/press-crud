<?php

namespace Roghumi\Press\Crud\Services\Generator;

use Exception;
use Illuminate\Console\Command;
use Reflection;
use ReflectionClass;
use ReflectionMethod;
use Reflector;
use Symfony\Component\Yaml\Yaml;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Illuminate\Support\Str;

class MakeComposite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:composite {def-file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make compositions for a resource with migration file';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $definitionFile = $this->argument('def-file');
        if (is_null($definitionFile)) {
            throw new Exception("Make composite commands needs a definition file path to work with.");
        }

        if (!file_exists($definitionFile)) {
            throw new Exception("Definition file not found at path: $definitionFile.");
        }

        $def = Yaml::parseFile($definitionFile);
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../../../resources/templates');
        $twig = new \Twig\Environment($loader, []);
        $outputDir = $def['output'] ?? resource_path('definitions/' . $def['resource']);
        $factoriesOutputDir = $def['factories']['output'] ?? $outputDir;
        if (!Str::endsWith($outputDir, DIRECTORY_SEPARATOR)) {
            $outputDir .= DIRECTORY_SEPARATOR;
        }
        if (!Str::endsWith($factoriesOutputDir, DIRECTORY_SEPARATOR)) {
            $factoriesOutputDir .= DIRECTORY_SEPARATOR;
        }

        $ref = new ReflectionClass(TwigFilters::class);
        $filters = $ref->getMethods(ReflectionMethod::IS_STATIC);
        foreach ($filters as $filter) {
            $twig->addFilter(new TwigFilter($filter->name, [TwigFilters::class, $filter->name]));
        }
        $ref = new ReflectionClass(TwigFunctions::class);
        $functions = $ref->getMethods(ReflectionMethod::IS_STATIC);
        foreach ($functions as $function) {
            $twig->addFunction(new TwigFunction($function->name, [TwigFunctions::class, $function->name]));
        }

        $templateFiles = [
            'resource' => 'Resource.php.twig',
            'provider' => 'ResourceProvider.php.twig',
            'composites' => [
                'CloneComposite.php.twig',
                'CreateComposite.php.twig',
                'QueryComposite.php.twig',
                'UpdateComposite.php.twig',
            ],
            'factory' => ['Factory.php.twig'],
        ];

        if ($def && is_array($def)) {
            foreach ($templateFiles as $template => $filename) {
                if (isset($def['ignoreFiles']) && !in_array($template, $def['ignoreFiles'])) {
                    if (is_array($filename)) {
                        foreach ($filename as $file) {
                            $this->renderFile($template, $file, $twig, $def, $outputDir, $factoriesOutputDir);
                        }
                    } else {
                        $this->renderFile($template, $filename, $twig, $def, $outputDir, $factoriesOutputDir);
                    }
                }
            }
        }
    }

    protected function renderFile($template, $filename, $twig, $def, $outputDir, $factoriesOutputDir)
    {
        $outputPath = $outputDir . substr($filename, 0, strlen($filename) - 5);
        if ($template === 'factory') {
            $outputPath = $factoriesOutputDir . substr($filename, 0, strlen($filename) - 5);
        }

        $fileContent = $twig->render($filename, $def);
        if (!file_exists($outputPath)) {
            file_put_contents(
                $outputPath,
                $fileContent
            );
        }
    }
}
