<?php

return [
    'verbs' => [
        'create' => [
            'success' => 'Successfully created record with id :recordId.',
        ],
        'update' => [
            'success' => 'Successfully updated record with id :recordId.',
        ],
        'clone' => [
            'success' => 'Successfully cloned record with id :sourceId into new records with ids :cloneIds.',
        ],
        'delete' => [
            'success' => 'Successfully deleted record with id :recordId.',
        ],
        'query' => [
            'success' => 'Query fetched :count records from :total records.',
        ],
        'undo_delete' => [
            'success' => 'Successfully recovered record with id :recordId.',
        ],
    ],
    'exceptions' => [
        'verb_not_found' => 'Crud verb :verb not found.',
        'resource_not_found' => 'Resource with id :id and provider class :class not found.',
        'provider_not_found' => 'Resource provider :class not be found.',
        'access_denied' => 'Access denied for verb :verb on resource :class.',
    ],
];
