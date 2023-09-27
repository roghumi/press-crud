<?php

namespace Roghumi\Press\Crud\Resources\Domain;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Roghumi\Press\Crud\Database\Factories\DomainFactory;
use Roghumi\Press\Crud\Services\CrudService\ICrudResource;
use Roghumi\Press\Crud\Services\DomainService\IDomain;
use Roghumi\Press\Crud\Services\DomainService\Traits\RBACDomainTrait;

/**
 * Domain Model
 *
 * @resource
 *
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @property int            $id
 * @property string         $name
 * @property int            $author_id
 * @property array          $data
 * @property Domain[]       $subdomains
 * @property Domain         $root_domain
 * @property UserAccount    $author
 */
class Domain extends Model implements ICrudResource, IDomain
{
    use HasFactory;
    use RBACDomainTrait;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'author_id',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Factory function
     *
     * @return Factory
     */
    protected static function newFactory(): Factory
    {
        return DomainFactory::new();
    }

    /**
     * get eloquent object for this resource item
     *
     * @return Model
     */
    public function getModel(): Model
    {
        return $this;
    }
}
