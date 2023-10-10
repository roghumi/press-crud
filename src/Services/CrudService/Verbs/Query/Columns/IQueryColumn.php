<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Columns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

interface IQueryColumn
{
    /**
     * Return name of column used for querying in database
     */
    public function getColumn(): string;

    /**
     * Return if this column can be sorted for a given request and args
     *
     * @param  mixed  ...$args
     */
    public function isSortable(Request $request, ...$args): bool;

    /**
     * Add sorting to the query on this column
     */
    public function sortQueryOnColumn(Builder $query, string $direction): void;
}
