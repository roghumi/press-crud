<?php

namespace Roghumi\Press\Crud\Services\RoleService\Commands;

use Illuminate\Console\Command;
use Roghumi\Press\Crud\Resources\Role\Role;

class UserRolesCommand extends Command
{
    protected $signature = 'user:roles {userId : The ID of the user}';
    protected $description = 'List roles for a user';

    public function handle()
    {
        $userId = $this->argument('userId');
        
        // Get user roles from database
        $userRoles = Role::join('role_user', 'roles.id', '=', 'role_user.role_id')
            ->where('role_user.user_id', $userId)
            ->select('roles.*')
            ->get();
        
        if ($userRoles->isEmpty()) {
            $this->info("User {$userId} has no roles assigned.");
            return;
        }
        
        $this->info("Roles for user {$userId}:");
        foreach ($userRoles as $role) {
            $this->line("- {$role->name} (ID: {$role->id})");
        }
    }
}
