<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SetupRolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Define permissions (granular so you can gate views/actions)
        $perms = [
            'properties.view',
            'properties.manage',
            'rooms.manage',
            'tasks.manage',
            'sessions.view',
            'sessions.manage',
            'sessions.view_all',
            'users.view',
            'users.manage',
            'roles.assign',
            'company.manage_owners',
            'company.manage_housekeepers',
            'reports.view',
            'reports.generate',
            'calendar.manage',
        ];

        foreach ($perms as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // Roles
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $owner = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
        $hk    = Role::firstOrCreate(['name' => 'housekeeper', 'guard_name' => 'web']);
        $company = Role::firstOrCreate(['name' => 'company', 'guard_name' => 'web']);

        // Attach permissions
        $admin->syncPermissions(Permission::all());

        // Company permissions - can manage owners and housekeepers under them
        $companyPerms = [
            'properties.view',
            'properties.manage',
            'rooms.manage',
            'tasks.manage',
            'sessions.view',
            'sessions.manage',
            'sessions.view_all',
            'users.view',
            'users.manage',
            'roles.assign',
            'company.manage_owners',
            'company.manage_housekeepers',
            'reports.view',
            'reports.generate',
            'calendar.manage',
        ];
        $company->syncPermissions($companyPerms);

        $ownerPerms = [
            'properties.view',
            'properties.manage',
            'rooms.manage',
            'tasks.manage',
            'sessions.view',
            'sessions.manage',
            'sessions.view_all',
            'users.view',
            'roles.assign',
            'reports.view',
            'reports.generate',
            'calendar.manage',
        ];
        $owner->syncPermissions($ownerPerms);

        $hkPerms = [
            'sessions.view',
            'sessions.manage',
        ];
        $hk->syncPermissions($hkPerms);
    }
}
