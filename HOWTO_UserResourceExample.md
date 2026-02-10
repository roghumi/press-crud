
### Sample Resource Configuration Example
Example resource provider for a simple CRUD endpoint for Users in a database, considering you already have a User Eloquent class.

```php
// Define a resource provider
class UserProvider implements ICrudResourceProvider
{
    public function getName(): string
    {
        return 'user';
    }
    
    public function getModelClass(): string
    {
        return User::class;
    }
    
    public function getAvailableVerbAndCompositions(): array
    {
        return [
            'query' => [new UserQueryComposite()],
            'create' => [new UserCreateComposite()],
            'update' => [new UserUpdateComposite()],
            'delete' => [new UserDeleteComposite()],
        ];
    }
}
```

#### UserQueryComposite Implementation Example
```php
class UserQueryComposite implements IQueryVerbComposite
{
    use QueryCompositeTrait;

    public function getColumns(Request $request, array $compositeColumns, ...$args): array
    {
        return array_merge($compositeColumns, [
            new QueryColumn([
                'name' => 'id',
                'label' => 'User ID',
                'sortable' => true,
                'filterable' => true,
                'searchable' => false,
            ]),
            new QueryColumn([
                'name' => 'name',
                'label' => 'Full Name',
                'sortable' => true,
                'filterable' => true,
                'searchable' => true,
            ]),
            new QueryColumn([
                'name' => 'email',
                'label' => 'Email Address',
                'sortable' => true,
                'filterable' => true,
                'searchable' => true,
            ]),
            new QueryColumn([
                'name' => 'created_at',
                'label' => 'Created At',
                'sortable' => true,
                'filterable' => true,
                'searchable' => false,
            ]),
        ]);
    }

    public function getFilters(Request $request, array $compositeFilters, ...$args): array
    {
        return array_merge($compositeFilters, [
            new WhereColumnEquals([
                'column' => 'status',
                'label' => 'Status',
            ]),
            new WhereColumnIn([
                'column' => 'role',
                'label' => 'Role',
            ]),
            new WhereColumnContains([
                'column' => 'email',
                'label' => 'Email Contains',
            ]),
        ]);
    }

    public function getRelations(Request $request, array $compositeRelations, ...$args): array
    {
        return array_merge($compositeRelations, [
            new QueryBelongsToRelation([
                'name' => 'profile',
                'label' => 'User Profile',
                'relation' => 'profile',
            ]),
            new QueryHasManyRelation([
                'name' => 'posts',
                'label' => 'User Posts',
                'relation' => 'posts',
            ]),
        ]);
    }

    public function getAggregateOptions(Request $request, array $compositeAggregations, ...$args): array
    {
        return array_merge($compositeAggregations, [
            'count' => 'Total Users',
            'max' => 'Maximum ID',
            'min' => 'Minimum ID',
        ]);
    }

    public function onBeforeQuery(Request $request, Builder $query, ...$args): void
    {
        // Apply global scopes or filters before query execution
        $query->where('active', true);
    }

    public function onAfterQuery(Request $request, Builder $query, LengthAwarePaginator $paginatedResult, ...$args): void
    {
        // Process or modify query results after they are retrieved
        // Example: Add computed fields or log query information
    }
}
```

#### UserCreateComposite Implementation Example
#### UserUpdateComposite Implementation Example
#### UserDeleteComposite Implementation Example

### Create Admin roles

### Create Admin users

### Test your api
