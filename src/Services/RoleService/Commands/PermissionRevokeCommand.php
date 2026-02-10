<?php

namespace Roghumi\Press\Crud\Services\RoleService\Commands;

use Illuminate\Console\Command;
use Roghumi\Press\Crud\Services\RoleService\IRoleService;

class PermissionRevokeCommand extends Command
{
    protected $signature = 'permission:revoke {roleId : The ID of the role} {permission : The name of the permission}';
    protected $description = 'Revoke a permission from a role';

    public function handle(IRoleService $roleService)
    {
        $roleId = $this->argument('roleId');
        $permission = $this->argument('permission');
        
        $roleService->detachPermissionsFromRole($roleId, [$permission]);
        
        $this->info("Permission '{$permission}' revoked from role {$roleId} successfully");
    }
}