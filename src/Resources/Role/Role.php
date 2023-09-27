<?php

namespace Roghumi\Press\Crud\Resources\Role;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Roghumi\Press\Crud\Database\Factories\RoleFactory;
use Roghumi\Press\Crud\Services\AccessService\IAccessRole;
use Roghumi\Press\Crud\Services\AccessService\IUser;
use Roghumi\Press\Crud\Services\CrudService\ICrudResource;
use Roghumi\Press\Crud\Services\RoleService\Traits\RBACRoleTrait;

/**
 * Role Model
 *
 * @resource
 *
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property int            $id
 * @property string         $name
 * @property int            $options
 * @property int            $author_id
 * @property IUser          $author
 */
class Role extends Model implements IAccessRole, ICrudResource
{
    use HasFactory;
    use RBACRoleTrait;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'author_id',
        'options',
    ];

    protected static function newFactory(): Factory
    {
        return RoleFactory::new();
    }

    /**
     * get eloquent object for this resource item
     */
    public function getModel(): Model
    {
        return $this;
    }
}
