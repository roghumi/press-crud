<?php

namespace Roghumi\Press\Crud\Resources\Group;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Roghumi\Press\Crud\Database\Factories\GroupFactory;
use Roghumi\Press\Crud\Services\AccessService\IUser;
use Roghumi\Press\Crud\Services\CrudService\ICrudResource;

/**
 * Group Model
 *
 * @resource
 *
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property \Carbon\Carbon|null $publish_at
 * @property \Carbon\Carbon|null $expire_at
 * @property int            $id
 * @property string         $name
 * @property int|null       $options
 * @property int            $parent_id
 * @property int            $author_id
 * @property IUser          $author
 * @property Group          $parent
 * @property array<Group>   $ancestors
 * @property array<Group>   $children
 * @property array<Group>   $siblings
 * @property array<Group>   $subtree
 */
class Group extends Model implements ICrudResource
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'parent_id',
        'author_id',
        'options',
    ];

    protected static function newFactory(): Factory
    {
        return GroupFactory::new();
    }

    /**
     * get eloquent object for this resource item
     */
    public function getModel(): Model
    {
        return $this;
    }
}
