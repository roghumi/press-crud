# Adding a New Verb

To add a new verb to LaraPress pipeline, you need to create a verb class that implements the `ICrudVerb` interface and register it with the system.

## Create Verb Class

1. Create a new class that implements `ICrudVerb` interface
2. Implement the required methods:
   - `getName()` - Returns verb name for RBAC
   - `getRouteForResource()` - Registers the appropriate route
   - `execRequest()` - Executes the verb logic  
   - `getSanitizedOutput()` - Formats the response
3. Add your verb class to the `verbs` array in the `config/press/crud.php` file

## Example Implementation

Here's a complete example of a "Publish" verb implementation:

```php
<?php

namespace Roghumi\Press\Crud\Verbs;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Roghumi\Press\Crud\Contracts\ICrudResourceProvider;
use Roghumi\Press\Crud\Contracts\ICrudVerb;
use Roghumi\Press\Crud\Exceptions\ResourceNotFoundException;
use Roghumi\Press\Crud\Helpers\UserHelpers;
use Roghumi\Press\Crud\Traits\RBACVerbTrait;

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

## Register Your Verb

Add your verb class to the `verbs` array in the `config/press/crud.php` file:

```php
'verbs' => [
    \Roghumi\Press\Crud\Services\CrudService\Verbs\Create\Create::class,
    \Roghumi\Press\Crud\Services\CrudService\Verbs\Update\Update::class,
    \Roghumi\Press\Crud\Services\CrudService\Verbs\Query\Query::class,
    \Roghumi\Press\Crud\Services\CrudService\Verbs\Export\Export::class,
    \Roghumi\Press\Crud\Services\CrudService\Verbs\Clone\CloneVerb::class,
    \Roghumi\Press\Crud\Services\CrudService\Verbs\Delete\Delete::class,
    \Roghumi\Press\Crud\Services\CrudService\Verbs\Restore\Restore::class,
    \Roghumi\Press\Crud\Verbs\PublishVerb::class,  // Add your custom verb here
],
```

## Add Verb to Admin Role Permissions

After adding your custom verb and registering it, you need to ensure that the admin role has permission to use it. The system automatically creates permissions for each verb-resource combination, but you must assign these permissions to the appropriate roles.

The permission names follow the pattern: `crud.{verb_name}.{resource_name}`

For example, for a "publish" verb on a "posts" resource, the permission would be: `crud.publish.posts`

You can assign this permission to the admin role using one of the following approaches:

1. **Database migration approach:**
   Add a migration to assign the permission to the admin role:
   ```php
   $adminRole = Role::where('name', 'admin')->first();
   $permission = Permission::where('name', 'crud.publish.posts')->first();
   $adminRole->permissions()->attach($permission);
   ```

2. **Command approach:**
   Many Laravel applications use a command to set up default permissions:
   ```bash
   php artisan role:grant admin crud.publish.posts
   ```

3. **Manual assignment in code:**
   ```php
   $adminRole = Role::where('name', 'admin')->first();
   $adminRole->givePermissionTo('crud.publish.posts');
   ```

## Use This Verb in Your Resources

To use your custom verb in a resource provider, add it to the `verbs` array of your resource:

```php
namespace App\Resources\Post;

class PostProvider implements ICrudResourceProvider
{
    public function getName(): string
    {
        return 'post';
    }
    
    public function getModelClass(): string
    {
        return Post::class;
    }
    
    public function getAvailableVerbAndCompositions(): array
    {
        return [
            'query' => [new PostQueryComposite()],
            'create' => [],
            'update' => [],
            'delete' => [],
            'publish' => [], // new verb for post resource
        ];
    }
}
```

## Test Your Verb Endpoint

You can test your custom verb endpoint with a curl command:

```bash
curl -X POST http://localhost/api/posts/1/publish \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Content-Type: application/json"
```

The response should contain:
```json
{
    "message": "Resource published successfully",
    "item": {
        "id": 1,
        "title": "Example Post",
        "published": true,
        // ... other fields
    }
}
```

For testing with a more complex verb, you might want to create a test using Laravel's testing helpers. Here's an example test:

```php
/** @test */
public function it_can_publish_a_post()
{
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);
    
    $response = $this->actingAs($user)
        ->postJson("/api/posts/{$post->id}/publish");
        
    $response->assertStatus(200);
    $response->assertJson(['message' => 'Resource published successfully']);
    $this->assertTrue($post->refresh()->published);
}
```
