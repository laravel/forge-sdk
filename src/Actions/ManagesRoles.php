<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Permission;
use Laravel\Forge\Resources\PredefinedRole;
use Laravel\Forge\Resources\Role;

trait ManagesRoles
{
    /**
     * Get the collection of predefined roles.
     *
     * @return PredefinedRole[]
     */
    public function predefinedRoles(): array
    {
        return $this->transformCollection(
            $this->get('predefined-roles')['data'] ?? [],
            PredefinedRole::class
        );
    }

    /**
     * Get a predefined role.
     */
    public function predefinedRole(int $roleId): PredefinedRole
    {
        return new PredefinedRole($this->get("predefined-roles/{$roleId}")['data'] ?? [], $this);
    }

    /**
     * Get the collection of permissions.
     *
     * @return Permission[]
     */
    public function permissions(): array
    {
        return $this->transformCollection(
            $this->get('permissions')['data'] ?? [],
            Permission::class
        );
    }

    /**
     * Get a permission.
     */
    public function permission(int $permissionId): Permission
    {
        return new Permission($this->get("permissions/{$permissionId}")['data'] ?? [], $this);
    }

    /**
     * Get the collection of roles for an organization.
     *
     * @return Role[]
     */
    public function roles(string $organizationSlug): array
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/roles")['data'] ?? [],
            Role::class,
            $organizationSlug,
        );
    }

    /**
     * Get a role.
     */
    public function role(string $organizationSlug, int $roleId): Role
    {
        return new Role($this->get("orgs/{$organizationSlug}/roles/{$roleId}")['data'] ?? [], $this);
    }

    /**
     * Create a new role.
     */
    public function createRole(string $organizationSlug, array $data): Role
    {
        return $this->newResource(
            Role::class,
            $this->post("orgs/{$organizationSlug}/roles", $data)['data'] ?? [],
            $organizationSlug,
        );
    }

    /**
     * Update a role.
     */
    public function updateRole(string $organizationSlug, int $roleId, array $data): Role
    {
        return new Role(
            $this->put("orgs/{$organizationSlug}/roles/{$roleId}", $data)['data'] ?? [],
            $this
        );
    }

    /**
     * Delete a role.
     */
    public function deleteRole(string $organizationSlug, int $roleId): void
    {
        $this->delete("orgs/{$organizationSlug}/roles/{$roleId}");
    }

    /**
     * Get the collection of permissions for a role.
     *
     * @return Permission[]
     */
    public function rolePermissions(string $organizationSlug, int $roleId): array
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/roles/{$roleId}/permissions")['data'] ?? [],
            Permission::class,
            $organizationSlug,
            extra: ['role_id' => $roleId],
        );
    }
}
