<?php

namespace Roghumi\Press\Crud\Resources\Domain;

use Illuminate\Database\Eloquent\Model;

/**
 * Domain Hierarchy
 *
 * @property int|string     $parent_id
 * @property int|string     $child_id
 * @property int            $depth
 */
class DomainHierarchy extends Model
{
    public $timestamps = false;

    public $table = 'domain_hierarchy';

    protected $fillable = [
        'parent_id',
        'child_id',
        'depth',
    ];
}
