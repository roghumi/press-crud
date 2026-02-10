<?php

return [
    // user definition, this is used by all press based packages
    'user' => [
        // user model class
        'class' => \App\Models\User::class,
        // user resource provider
        'provider' => '',
        // user table name in database
        'table' => 'users',
    ],

    /**
     * available verbs for all the resources, each resource will
     * have its own list of available verbs name and composites
     * which its class should be included here
     */
    'verbs' => [
        \Roghumi\Press\Crud\Services\CrudService\Verbs\Create\Create::class,
        \Roghumi\Press\Crud\Services\CrudService\Verbs\Update\Update::class,
        \Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Query::class,
        \Roghumi\Press\Crud\Services\CrudService\Verbs\Export\Export::class,
        \Roghumi\Press\Crud\Services\CrudService\Verbs\Clone\CloneVerb::class,
        \Roghumi\Press\Crud\Services\CrudService\Verbs\Delete\Delete::class,
        \Roghumi\Press\Crud\Services\CrudService\Verbs\Restore\Restore::class,
    ],

    // available resources, put provider class names here
    'resources' => [
        \Roghumi\Press\Crud\Resources\Domain\DomainProvider::class,
    ],

    // list of middleware to use on crud routes
    'middleware' => [
        'api',
        \Roghumi\Press\Crud\Http\Middleware\RoleBasedAccessControl::class,
    ],

    // prefix for crud routes
    'prefix' => 'api',

    // query verb options
    'query' => [
        // max limit per page for query request
        'maxPerPage' => 10000,
        // default per page for query request
        'perPage' => 10,
        // available exporters for export verb
        'export_formatters' => [
            'csv' => \Roghumi\Press\Crud\Services\CrudService\Verbs\Export\Formatters\CSVExportFormatter::class,
        ],
    ],

    // list of available relation ships when using generator service
    'relations' => [
        'BelongsTo' => [
            \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
            \Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Relations\QueryBelongsToRelation::class
        ],
        'BelongsToMany' => [
            \Illuminate\Database\Eloquent\Relations\BelongsToMany::class,
            \Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Relations\QueryBelongsToManyRelation::class
        ],
        'HasMany' => [
            \Illuminate\Database\Eloquent\Relations\HasMany::class,
            \Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Relations\QueryHasManyRelation::class
        ],
        'HasOne' => [
            \Illuminate\Database\Eloquent\Relations\HasOne::class,
            \Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Relations\QueryHasOneRelation::class
        ],
        'HasManyThrough' => [
            \Illuminate\Database\Eloquent\Relations\HasManyThrough::class,
            \Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Relations\QueryHasManyThroughRelation::class
        ],
        'MorphTo' => [
            \Illuminate\Database\Eloquent\Relations\MorphTo::class,
            \Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Relations\QueryMorphToRelation::class
        ],
        'MorphOne' => [
            \Illuminate\Database\Eloquent\Relations\MorphOne::class,
            \Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Relations\QueryMorphOneRelation::class
        ],
        'MorphMany' => [
            \Illuminate\Database\Eloquent\Relations\MorphMany::class,
            \Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Relations\QueryMorphManyRelation::class
        ],
        'MorphToMany' => [
            \Illuminate\Database\Eloquent\Relations\MorphToMany::class,
            \Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Relations\QueryMorphToManyRelation::class
        ],
    ],

    // available resource query filters.
    'filters' => [
        'in' => \Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Filters\WhereColumnIn::class,
        'equals' => \Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Filters\WhereColumnEquals::class,
        'between' => \Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Filters\WhereColumnBetween::class,
        'contains' => \Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Filters\WhereColumnContains::class,
    ],

    // allow cross origin access to domains // comment out for disabling cr origin.
    'cross-origin-allow' => [
        '*'
    ]
];
