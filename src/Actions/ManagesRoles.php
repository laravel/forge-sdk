<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

use Laravel\Forge\CursorPaginator;
use Laravel\Forge\Resources\Permission;
use Laravel\Forge\Resources\PredefinedRole;
use Laravel\Forge\Resources\Role;

trait ManagesRoles
{
    /**
     * Get the collection of predefined roles.
     */
    public function predefinedRoles(): CursorPaginator
    {
        return $this->paginatedCollection(
            'predefined-roles',
            PredefinedRole::class,
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
     */
    public function permissions(): CursorPaginator
    {
        return $this->paginatedCollection(
            'permissions',
            Permission::class,
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
     */
    public function roles(string $organizationSlug): CursorPaginator
    {
        return $this->paginatedCollection(
            "orgs/{$organizationSlug}/roles",
            Role::class,
            $organizationSlug,
        );
    }

    /**
     * Get a role.
     */
    public function role(string $organizationSlug, int $roleId): Role
    {
        return $this->newResource(
            Role::class,
            $this->get("orgs/{$organizationSlug}/roles/{$roleId}")['data'] ?? [],
            $organizationSlug,
        );
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
        return $this->newResource(
            Role::class,
            $this->put("orgs/{$organizationSlug}/roles/{$roleId}", $data)['data'] ?? [],
            $organizationSlug,
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
     */
    public function rolePermissions(string $organizationSlug, int $roleId): CursorPaginator
    {
        return $this->paginatedCollection(
            "orgs/{$organizationSlug}/roles/{$roleId}/permissions",
            Permission::class,
            $organizationSlug,
            extra: ['role_id' => $roleId],
        );
    }
}
