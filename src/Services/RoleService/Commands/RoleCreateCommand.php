<?php

namespace Roghumi\Press\Crud\Services\RoleService\Commands;

use Illuminate\Console\Command;
use Roghumi\Press\Crud\Resources\Role\Role;

class RoleCreateCommand extends Command
{
    protected $signature = 'role:create {name : The name of the role}';
    protected $description = 'Create a new role';

    public function handle()
    {
        $name = $this->argument('name');
        
        // Check if role already exists
        $existingRole = Role::where('name', $name)->first();
        if ($existingRole) {
            $this->error("Role '{$name}' already exists with ID: {$existingRole->id}");
            return;
        }
        
        // Create a new role in the database
        $role = Role::create([
            'name' => $name,
            'author_id' => auth()->id() ?? 1,
        ]);
        
        $this->info("Role '{$name}' created successfully with ID: {$role->id}");
    }
}