<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Columns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class QueryColumn implements IQueryColumn
{
    /**
     * @return QueryColumn
     */
    public function __construct(
        public string $name,
        public bool $sortable,
    ) {
    }

    /**
     * Create new column model
     *
     *
     * @return QueryColumn
     */
    public static function create(string $name, bool $sortable)
    {
        return new QueryColumn($name, $sortable);
    }

    /**
     * Return name of column used for querying in database
     */
    public function getColumn(): string
    {
        return $this->name;
    }

    /**
     * Return if this column can be sorted for a given request and args
     *
     * @param  mixed  ...$args
     */
    public function isSortable(Request $request, ...$args): bool
    {
        return $this->sortable;
    }

    /**
     * Add sorting to the query on this column
     */
    public function sortQueryOnColumn(Builder $query, string $direction): void
    {
        $query->orderBy($this->name, $direction === 'asc' ? 'ASC' : 'DESC');
    }
}
