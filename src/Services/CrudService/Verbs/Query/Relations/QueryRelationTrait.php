<?php

namespace Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Relations;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Roghumi\Press\Crud\Services\CrudService\ICrudResourceProvider;
use Roghumi\Press\Crud\Services\CrudService\ICrudVerbComposite;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Columns\IQueryColumn;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\IQueryVerbComposite;
use Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Query;

trait QueryRelationTrait
{
    public ICrudResourceProvider $provider;

    /** @var ICrudVerbComposite[] */
    public array $queryComposites;

    /** @var IQueryRelation[] */
    public array $availableRelations;

    /** @var IQueryColumn[] */
    public array $availableColumns;

    public function __construct(
        Request $request,
        public string $name,
        public string $providerClass,
        ...$args,
    ) {
        $this->loadProviderWithClassName($request, $providerClass, ...$args);
    }

    protected function loadProviderWithClassName(Request $request, $providerClass, ...$args)
    {
        /** @var ICrudResourceProvider */
        $this->provider = new $providerClass();
        $composites = $this->provider->getAvailableVerbAndCompositions();
        if (isset($composites[Query::NAME])) {
            $this->queryComposites = $composites[Query::NAME];
        } else {
            $this->queryComposites = [];
        }

        $availableRelations = [];
        $availableColumns = [];
        /** @var ICrudVerbComposite $composite */
        foreach ($this->queryComposites as $compositeClass) {
            $composite = new $compositeClass();
            if ($composite instanceof IQueryVerbComposite) {
                $availableColumns = $composite->getColumns($request, $availableColumns, ...$args);
                $availableRelations = $composite->getRelations($request, $availableRelations, ...$args);
            }
        }

        $this->availableColumns = $availableColumns;
        $this->availableRelations = $availableRelations;
    }

    protected function getColumnNames(): array
    {
        return Collection::make($this->availableColumns)->pluck('name')->toArray();
    }

    public function getRelationNames(): array
    {
        return Collection::make($this->availableRelations)->pluck('name')->toArray();
    }

    public function validateRelationRequestParams(array $data): array
    {
        return Validator::validate($data, [
            'columns' => ['array', 'nullable'],
            'columns.*' => ['string', Rule::in($this->getColumnNames())],
            'relations' => ['array', 'nullable'],
            'relations.*' => ['string', Rule::in($this->getRelationNames())],
        ]);
    }
}
