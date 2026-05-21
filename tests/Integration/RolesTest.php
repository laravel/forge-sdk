<?php

declare(strict_types=1);

namespace Tests\Integration;

use Laravel\Forge\Resources\Permission;
use Laravel\Forge\Resources\PredefinedRole;
use Laravel\Forge\Resources\Role;

class RolesTest extends IntegrationTestCase
{
    public function test_list_predefined_roles(): void
    {
        $roles = $this->forge()->predefinedRoles();

        $this->assertIsArray($roles);
        $this->assertNotEmpty($roles);

        $role = $roles[0];
        $this->assertInstanceOf(PredefinedRole::class, $role);
        $this->assertIsInt($role->id);
        $this->assertIsString($role->name);
        $this->assertNotEmpty($role->name);

        // Envelope keys stripped
        $this->assertIsArray($role->relationships);
        $this->assertIsArray($role->links);
    }

    public function test_list_permissions(): void
    {
        $permissions = $this->forge()->permissions();

        $this->assertIsArray($permissions);
        $this->assertNotEmpty($permissions);

        $permission = $permissions[0];
        $this->assertInstanceOf(Permission::class, $permission);
        $this->assertIsInt($permission->id);
        $this->assertIsString($permission->name);
        $this->assertNotEmpty($permission->name);
    }

    public function test_list_organization_roles(): void
    {
        $roles = $this->forge()->roles($this->organization());

        $this->assertIsArray($roles);

        if (count($roles) > 0) {
            $role = $roles[0];
            $this->assertInstanceOf(Role::class, $role);
            $this->assertIsInt($role->id);
            $this->assertIsString($role->name);
        }
    }

    public function test_crud_role(): void
    {
        $org = $this->organization();
        $suffix = time();

        // The API validates permissions against the Permission enum (string values),
        // not numeric IDs. Use the permission name (enum value).
        $permissions = $this->forge()->permissions();
        $permissionName = $permissions[0]->name;

        // Create
        $role = $this->forge()->createRole($org, [
            'name' => "SDK Test Role {$suffix}",
            'description' => 'Created by integration tests - safe to delete',
            'permissions' => [$permissionName],
        ]);

        $this->assertInstanceOf(Role::class, $role);
        $this->assertIsInt($role->id);
        $this->assertSame("SDK Test Role {$suffix}", $role->name);

        try {
            usleep(500_000);

            // Read
            $fetched = $this->forge()->role($org, $role->id);
            $this->assertSame($role->id, $fetched->id);

            // Envelope keys stripped
            $this->assertIsArray($fetched->relationships);
            $this->assertIsArray($fetched->links);

            usleep(500_000);

            // Update
            $updated = $this->forge()->updateRole($org, $role->id, [
                'name' => "SDK Test Role {$suffix} Updated",
                'description' => 'Updated by integration tests',
                'permissions' => [$permissionName],
            ]);

            $this->assertSame("SDK Test Role {$suffix} Updated", $updated->name);
        } finally {
            usleep(500_000);

            // Delete (always clean up)
            $this->forge()->deleteRole($org, $role->id);
        }
    }
}
