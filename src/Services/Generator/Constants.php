<?php

namespace Roghumi\Press\Crud\Services\Generator;

use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Filters\WhereColumnBetween;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Filters\WhereColumnContains;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Filters\WhereColumnEquals;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Filters\WhereColumnIn;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Relations\QueryBelongsToRelation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Constants
{
    public const RelationsDictionary = [
        'BelongsTo' => [BelongsTo::class, QueryBelongsToRelation::class],
        'BelongsToMany' => [BelongsToMany::class, QueryBelongsToRelation::class],
        'HasMany' => [HasMany::class, QueryBelongsToRelation::class],
        'HasOne' => [HasOne::class, QueryBelongsToRelation::class],
        'HasManyThrough' => [HasManyThrough::class, QueryBelongsToRelation::class],
        'MorphTo' => [MorphTo::class, QueryBelongsToRelation::class],
        'MorphOne' => [MorphOne::class, QueryBelongsToRelation::class],
        'MorphMany' => [MorphMany::class, QueryBelongsToRelation::class],
    ];
    public const FiltersDictionary = [
        'in' => WhereColumnIn::class,
        'equals' => WhereColumnEquals::class,
        'between' => WhereColumnBetween::class,
        'contains' => WhereColumnContains::class,
    ];
    public const RelationClassDictionary = [
        'user.class' => "config('press.crud.user.class')",
    ];
}
