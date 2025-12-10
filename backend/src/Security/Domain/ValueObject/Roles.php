<?php

/**
 * @author Emile Camara <camara.emile@gmail.com>
 *
 * @project  defi-fullstack-app
 */

declare(strict_types=1);

namespace App\Security\Domain\ValueObject;

use App\Shared\Domain\Collection;

/**
 * @extends Collection<Role>
 */
class Roles extends Collection
{
    protected function type(): string
    {
        return Role::class;
    }

    public function hasRole(Role $role): bool
    {
        return in_array($role, $this->items(), true);
    }

    /**
     * @param array<string> $roles
     */
    public static function fromArray(array $roles): self
    {
        $roleObjects = array_map(
            fn (string $role): Role => Role::fromString($role),
            $roles
        );

        return new self($roleObjects);
    }

    /**
     * @return array<string>
     */
    public function toArray(): array
    {
        return array_map(
            fn (Role $role) => $role->value,
            $this->items()
        );
    }
}
