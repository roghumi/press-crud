<?php

namespace Roghumi\Press\Crud\Commands;

use Exception;
use Illuminate\Console\Command;
use Symfony\Component\Yaml\Yaml;

class MakeComposite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:composite {def} {output-dir} {--diff}';

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
        $definitionFile = $this->argument('def');
        $outputDir = $this->argument('output-dir');
        if (is_null($definitionFile) || is_null($outputDir)) {
            throw new Exception("Make composite commands needs a definition file path & output path to work with.");
        }

        if (!file_exists($definitionFile)) {
            throw new Exception("Definition file not found at path: $definitionFile.");
        }

        $def = Yaml::parseFile($definitionFile);
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/../../resources/templates');
        $twig = new \Twig\Environment($loader, []);

        if ($def && is_array($def)) {
            $resourceDotPHP = $outputDir . $def['resource'] . '.php';

            dd($twig->render('CreateComposite.php.twig', $def));
            if (!file_exists($resourceDotPHP)) {
                file_put_contents(
                    $resourceDotPHP,
                    ""
                );
            }
        }
    }
}
