<?php

namespace Roghumi\Press\Crud\Services\Generator;

class TwigFunctions
{
    public static function relationsUsedClasses(array $relations): string
    {
        $lines = [];

        foreach ($relations as $relation) {
            $rt = $relation['type'];
            $types = config(`press.crud.relations.$rt`) ?? null;
            $relationType = $types[0] ?? null;

            if ($relationType) {
                $lines[] = "use $relationType;";
            }
        }

        return implode("\n", $lines);
    }

    public static function queryUsedClasses(array $relations, array $columns): string
    {
        $lines = [];

        foreach ($relations as $relation) {
            $rt = $relation['type'];
            $types = config(`press.crud.relations.$rt`) ?? null;
            $queryRelationType = $types[1] ?? null;

            if ($queryRelationType) {
                $lines[] = "use $queryRelationType;";
            }
        }

        foreach ($columns as $column) {
            $filters = $column['filters'] ?? [];
            foreach ($filters as $filter) {
                $type = config(`press.crud.filters.$filter`) ?? null;
                if ($type) {
                    $lines[] = "use $type;";
                }
            }
        }

        return implode("\n", $lines);
    }

    public static function verbsUsedClasses(array $verbs): string
    {
        $lines = [];

        $avVerbClasses = config('press.crud.verbs');
        $avVerbs = [];
        foreach ($avVerbClasses as $verbClass) {
            /** @var ICRudVerb */
            $verb = new $verbClass();
            $avVerbs[$verb->getName()] = $verb;
        }

        foreach ($verbs as $verb) {
            if (is_array($verb)) {
                $verbName = $verb['verb'];
            } elseif (is_string($verb)) {
                $verbName = $verb;
            }
            if (isset($avVerbs[$verbName])) {
                $classNamespace = $avVerbs[$verbName]::class;

                $lines[] = "use $classNamespace;";
            }
        }

        return implode("\n", $lines);
    }
}
