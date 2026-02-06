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
     * @param  string  $organizationSlug
     * @return \Laravel\Forge\Resources\Role[]
     */
    public function roles($organizationSlug)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/roles")['data'] ?? [],
            Role::class,
            ['organization_id' => $organizationSlug]
        );
    }

    /**
     * Get a role.
     *
     * @param  string  $organizationSlug
     * @param  string  $roleId
     * @return \Laravel\Forge\Resources\Role
     */
    public function role($organizationSlug, $roleId)
    {
        return new Role($this->get("orgs/{$organizationSlug}/roles/{$roleId}")['data'] ?? [], $this);
    }

    /**
     * Create a new role.
     *
     * @param  string  $organizationSlug
     * @return \Laravel\Forge\Resources\Role
     */
    public function createRole($organizationSlug, array $data)
    {
        $role = $this->post("orgs/{$organizationSlug}/roles", $data)['data'] ?? [];

        return new Role($role + ['organization_id' => $organizationSlug], $this);
    }

    /**
     * Update a role.
     *
     * @param  string  $organizationSlug
     * @param  string  $roleId
     * @return \Laravel\Forge\Resources\Role
     */
    public function updateRole($organizationSlug, $roleId, array $data)
    {
        $role = $this->put("orgs/{$organizationSlug}/roles/{$roleId}", $data)['data'] ?? [];

        return new Role($role, $this);
    }

    /**
     * Delete a role.
     *
     * @param  string  $organizationSlug
     * @param  string  $roleId
     * @return void
     */
    public function deleteRole($organizationSlug, $roleId)
    {
        $this->delete("orgs/{$organizationSlug}/roles/{$roleId}");
    }

    /**
     * Get the collection of permissions for a role.
     *
     * @param  string  $organizationSlug
     * @param  string  $roleId
     * @return \Laravel\Forge\Resources\Permission[]
     */
    public function rolePermissions($organizationSlug, $roleId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/roles/{$roleId}/permissions")['data'] ?? [],
            Permission::class,
            ['organization_id' => $organizationSlug, 'role_id' => $roleId]
        );
    }
}
