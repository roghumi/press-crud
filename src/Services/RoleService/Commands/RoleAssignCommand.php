<?php

namespace Roghumi\Press\Crud\Services\RoleService\Commands;

use Illuminate\Console\Command;
use Roghumi\Press\Crud\Services\RoleService\IRoleService;

class RoleAssignCommand extends Command
{
    protected $signature = 'role:assign {userId : The ID of the user} {roleId : The ID of the role}';
    protected $description = 'Assign a role to a user';

    public function handle(IRoleService $roleService)
    {
        $userId = $this->argument('userId');
        $roleId = $this->argument('roleId');
        
        $roleService->attachRoleToUser($roleId, $userId);
        
        $this->info("Role {$roleId} assigned to user {$userId} successfully");
    }
}