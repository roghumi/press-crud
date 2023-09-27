<?php

namespace Roghumi\Press\Crud\Resources\Group;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Roghumi\Press\Crud\Services\AccessService\IUser;

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
 */
class GroupHierarchy extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'child_id',
        'depth',
    ];
}
