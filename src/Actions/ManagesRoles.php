<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Permission;
use Laravel\Forge\Resources\PredefinedRole;
use Laravel\Forge\Resources\Role;

trait ManagesRoles
{
    /**
     * Get the collection of predefined roles.
     *
     * @return \Laravel\Forge\Resources\PredefinedRole[]
     */
    public function predefinedRoles()
    {
        return $this->transformCollection(
            $this->get('predefined-roles')['data'] ?? [],
            PredefinedRole::class
        );
    }

    /**
     * Get a predefined role.
     *
     * @param  string  $roleId
     * @return \Laravel\Forge\Resources\PredefinedRole
     */
    public function predefinedRole($roleId)
    {
        return new PredefinedRole($this->get("predefined-roles/{$roleId}")['data'] ?? [], $this);
    }

    /**
     * Get the collection of permissions.
     *
     * @return \Laravel\Forge\Resources\Permission[]
     */
    public function permissions()
    {
        return $this->transformCollection(
            $this->get('permissions')['data'] ?? [],
            Permission::class
        );
    }

    /**
     * Get a permission.
     *
     * @param  string  $permissionId
     * @return \Laravel\Forge\Resources\Permission
     */
    public function permission($permissionId)
    {
        return new Permission($this->get("permissions/{$permissionId}")['data'] ?? [], $this);
    }

    /**
     * Get the collection of roles for an organization.
     *
     * @param  string  $organizationId
     * @return \Laravel\Forge\Resources\Role[]
     */
    public function roles($organizationId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/roles")['data'] ?? [],
            Role::class,
            ['organization_id' => $organizationId]
        );
    }

    /**
     * Get a role.
     *
     * @param  string  $organizationId
     * @param  string  $roleId
     * @return \Laravel\Forge\Resources\Role
     */
    public function role($organizationId, $roleId)
    {
        return new Role($this->get("orgs/{$organizationId}/roles/{$roleId}")['data'] ?? [], $this);
    }

    /**
     * Create a new role.
     *
     * @param  string  $organizationId
     * @return \Laravel\Forge\Resources\Role
     */
    public function createRole($organizationId, array $data)
    {
        $role = $this->post("orgs/{$organizationId}/roles", $data)['data'] ?? [];

        return new Role($role + ['organization_id' => $organizationId], $this);
    }

    /**
     * Update a role.
     *
     * @param  string  $organizationId
     * @param  string  $roleId
     * @return \Laravel\Forge\Resources\Role
     */
    public function updateRole($organizationId, $roleId, array $data)
    {
        $role = $this->put("orgs/{$organizationId}/roles/{$roleId}", $data)['data'] ?? [];

        return new Role($role, $this);
    }

    /**
     * Delete a role.
     *
     * @param  string  $organizationId
     * @param  string  $roleId
     * @return void
     */
    public function deleteRole($organizationId, $roleId)
    {
        $this->delete("orgs/{$organizationId}/roles/{$roleId}");
    }

    /**
     * Get the collection of permissions for a role.
     *
     * @param  string  $organizationId
     * @param  string  $roleId
     * @return \Laravel\Forge\Resources\Permission[]
     */
    public function rolePermissions($organizationId, $roleId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/roles/{$roleId}/permissions")['data'] ?? [],
            Permission::class,
            ['organization_id' => $organizationId, 'role_id' => $roleId]
        );
    }
}
