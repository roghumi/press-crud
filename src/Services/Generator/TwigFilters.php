<?php

namespace Roghumi\Press\Crud\Services\Generator;

use Roghumi\Press\Crud\Services\CrudService\ICrudVerb;
use Illuminate\Support\Str;

class TwigFilters
{
    public static function ruleFromDef(array $ruleDef, string $colName, string $ruleClass)
    {
        if (isset($ruleDef['unique'])) {
            $table = $ruleDef['unique']['table'] ?? 'table';
            $column = $ruleDef['unique']['column'] ?? $colName;
            $ruleString = "Rule::unique('$table', '$column')";
            if (isset($ruleDef['update']) && $ruleClass === 'update') {
                $argsIndex = 0;
                if (Str::startsWith($ruleDef['update'], 'exclude-args-')) {
                    $argsIndex = substr($ruleDef['update'], strlen('exclude-args-'));
                }
                $ruleString .= '->ignore($args[' . $argsIndex . '])';
            }

            return $ruleString;
        }
    }
    public static function ruleString(mixed $rules, string $colName, string $ruleClass)
    {
        if (is_string($rules)) {
            return "'$rules'";
        } elseif (is_array($rules)) {
            $ruleStrings = [];
            foreach ($rules as $rule) {
                if (is_string($rule)) {
                    $ruleStrings[] = "'$rule'";
                } elseif (is_array($rule)) {
                    $ruleStrings[] = self::ruleFromDef($rule, $colName, $ruleClass);
                }
            };
            $rulesArrayElements = implode(', ', $ruleStrings);
            return "[$rulesArrayElements]";
        }

        return 'unknown-rule';
    }
    public static function rules(array $columns, string $ruleClass)
    {
        $rules = [];

        foreach ($columns as $column) {
            if (!isset($column['input']) || !in_array($column['input'], [false, 'auth_id'])) {
                $key = $column['key'] ?? $column['name'];
                $ruleString = self::ruleString($column['rules'] ?? [], $column['name'], $ruleClass);
                $rules[] = "'$key' => $ruleString";
            }
        }
        return implode(",\n", $rules);
    }
    public static function createRules(array $columns): string
    {
        return self::rules($columns, 'create');
    }
    public static function updateRules(array $columns): string
    {
        return self::rules($columns, 'update');
    }
    public static function cloneRules(array $columns): string
    {
        return self::rules($columns, 'clone');
    }
    public static function createSanitizeInputs(array $columns): string
    {
        $lines = [];

        foreach ($columns as $column) {
            if (!isset($column['input']) || !in_array($column['input'] ?? null, [false, 'auth_id'])) {
                $key = $column['key'] ?? $column['name'];
                $name = $column['name'];
                $default = $column['default'] ?? 'null';
                $lines[] = "'$name' => \$request->get('$key', $default)";
            }
        }
        return implode(",\n", $lines);
    }
    public static function updateSanitizeInputs(array $columns): string
    {
        $lines = [];

        foreach ($columns as $column) {
            if ($column['nullable'] ?? false) {
                $key = $column['key'] ?? $column['name'];
                $name = $column['name'];
                $lines[] = "if (\$request->has('$key')) \$compositeData['$name'] = \$request->get('$key');";
            }
        }

        return implode("\n", $lines);
    }
    public static function queryColumns(array $columns, $timestamps = false, $softDeletes = false): string
    {
        $lines = [];

        foreach ($columns as $column) {
            $name = $column['name'];
            $sortable = $column['sort'] ?? false ? 'true' : 'false';
            $lines[] = "QueryColumn::create('$name', $sortable)";
        }
        if ($timestamps) {
            if (is_array($timestamps)) {
                foreach ($timestamps as $timestamp) {
                    $lines[] = "QueryColumn::create('$timestamp', true)";
                }
            } elseif (is_string($timestamps)) {
                $lines[] = "QueryColumn::create('$timestamps', true)";
            } else {
                $lines[] = "QueryColumn::create('created_at', true)";
                $lines[] = "QueryColumn::create('updated_at', true)";
            }
        }
        if ($softDeletes) {
            if (is_string($softDeletes)) {
                $lines[] = "QueryColumn::create('$softDeletes', true)";
            } else {
                $lines[] = "QueryColumn::create('deleted_at', true)";
            }
        }

        return implode(",\n", $lines);
    }
    public static function queryRelations(array $relations): string
    {
        $lines = [];

        foreach ($relations as $relation) {
            $types = Constants::RelationsDictionary[$relation['type']] ?? null;
            $name = $relation['name'];
            $type = class_basename($types[0] ?? null);
            $queryRelationType = class_basename($types[1] ?? null);
            $provider = $relation['provider'];
            $class = $relation['class'];
            if ($provider === 'user.provider') {
                $provider = "config('press.crud.user.provider')";
            }
            if ($class === 'user.class') {
                $class = "config('press.crud.user.class')";
            }

            $lines[] = "$queryRelationType::create('$name', $provider, \$request, ...\$args)";
        }

        return implode(",\n", $lines);
    }
    public static function queryFilters(array $columns): string
    {
        $lines = [];

        foreach ($columns as $column) {
            $filters = $column['filters'] ?? [];
            foreach ($filters as $filter) {
                $type = class_basename(Constants::FiltersDictionary[$filter]);
                $name = $column['name'] . '.' . $filter;
                $colName = $column['name'];
                $lines[] = "$type::create('$name', '$colName')";
            }
        }

        return implode(",\n", $lines);
    }
    public static function queryAggregates(array $columns): string
    {
        $lines = [];
        return implode(",\n", $lines);
    }
    public static function providerVerbs(array $verbs): string
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
                $compos = $verb['comp'] ?? [];
            } elseif (is_string($verb)) {
                $verbName = $verb;
                $compos = [];
            }
            if (isset($avVerbs[$verbName])) {
                $className = class_basename($avVerbs[$verbName]);
                $line = "$className::class => [";
                if (is_array($compos)) {
                    foreach ($compos as $com) {
                        $line .= "$com::class, ";
                    }
                } elseif (is_string($compos)) {
                    $line .= "$compos::class";
                }
                $line .= "]";
                $lines[] = $line;
            }
        }

        return implode(",\n", $lines);
    }
    public static function relationClass(string $basename): string
    {
        return Constants::RelationClassDictionary[$basename] ?? self::ucFirst($basename);
    }
    public static function fakerEval($faker)
    {
        if (is_array($faker)) {
            if (isset($faker['function'])) {
                $function = $faker['function'];
                $params = $faker['params'] ?? [];
                $functionParams = implode(',', $params);
                return "fake()->$function($functionParams)";
            }
        } else {
            return $faker;
        }
    }
    public static function toLower($str)
    {
        return Str::lower($str);
    }
    public static function toUpper($str)
    {
        return Str::upper($str);
    }
    public static function camelCase($str)
    {
        return Str::camel($str);
    }
    public static function ucFirst($str)
    {
        return Str::ucfirst($str);
    }
}
