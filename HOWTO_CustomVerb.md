# Adding a New Verb
To add a new verb to LaraPress pipeline:

## Create Verb Class
1. Create a new class that implements `ICrudVerb` interface
2. Implement the required methods:
   - `getName()` - Returns verb name for RBAC
   - `getRouteForResource()` - Registers the appropriate route
   - `execRequest()` - Executes the verb logic
   - `getSanitizedOutput()` - Formats the response

3. Add your verb class to the `verbs` array in the `config/press/crud.php` file

Example implementation of a simple "Publish" verb:

```php
class PublishVerb implements ICrudVerb
{
    use RBACVerbTrait;

    public const NAME = 'publish';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getRouteForResource(ICrudResourceProvider $provider): Route
    {
        return $this->registerRouteWithControl(
            $provider,
            ['POST'],
            sprintf('%s/{id}/publish', $provider->getName())
        );
    }

    public function execRequest(Request $request, ICrudResourceProvider $provider, ...$args): mixed
    {
        return $this->execRouteWithControl(
            $request,
            $provider,
            // execution callback
            function (array $sanitizedData, array $verbCompositions) use ($request, $args, $provider) {
                $modelId = $args[0];
                $model = $provider->getObjectById($modelId)?->getModel();
                
                if (!$model) {
                    throw new ResourceNotFoundException($modelId, $provider::class);
                }
                
                $model->update(['published' => true]);
                
                return $model;
            },
            // event callback
            function ($result) use ($provider) {
                PublishEvent::dispatch(
                    UserHelpers::getAuthUserId(),
                    get_class($provider),
                    $result->id,
                    time()
                );
            },
            // composite callback
            null,
            // validation
            true,
            // transaction
            true,
            // args
            ...$args
        );
    }

    public function getSanitizedOutput(Request $request, mixed $execResult): array
    {
        return [
            'message' => 'Resource published successfully',
            'item' => $execResult->toArray(),
        ];
    }
}
```

## Register your class in `press.crud` config file
@todo: describe how

## Use this verb in your resources
@todo: show example resource provider with this custom Publish verb

## Test your verb endpoint
@todo: draft a curl command to test this newly created custom verb for example resource