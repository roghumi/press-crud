<?php

namespace Roghumi\Press\Crud\Services\RoleService\Commands;

use Illuminate\Console\Command;
use Roghumi\Press\Crud\Services\RoleService\IRoleService;

class RoleRevokeCommand extends Command
{
    protected $signature = 'role:revoke {userId : The ID of the user} {roleId : The ID of the role}';
    protected $description = 'Revoke a role from a user';

    public function handle(IRoleService $roleService)
    {
        $userId = $this->argument('userId');
        $roleId = $this->argument('roleId');
        
        $roleService->detachRoleFromUser($roleId, $userId);
        
        $this->info("Role {$roleId} revoked from user {$userId} successfully");
    }
}
