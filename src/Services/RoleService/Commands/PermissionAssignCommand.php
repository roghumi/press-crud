<?php

namespace Roghumi\Press\Crud\Services\RoleService\Commands;

use Illuminate\Console\Command;
use Roghumi\Press\Crud\Services\RoleService\IRoleService;

class PermissionAssignCommand extends Command
{
    protected $signature = 'permission:assign {roleId : The ID of the role} {permission : The name of the permission}';
    protected $description = 'Assign a permission to a role';

    public function handle(IRoleService $roleService)
    {
        $roleId = $this->argument('roleId');
        $permission = $this->argument('permission');
        
        $roleService->attachPermissionsToRole($roleId, [$permission]);
        
        $this->info("Permission '{$permission}' assigned to role {$roleId} successfully");
    }
}