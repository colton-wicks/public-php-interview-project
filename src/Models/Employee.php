<?php

declare(strict_types=1);

namespace App\Models;

class Employee
{
    public function __construct(
        public readonly int $id,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $title,
        public readonly string $timezone,
        public readonly bool $active,
        public readonly ?int $managerId
    ) {}

    /**
     * Builds an Employee from a database row
     * @param array<string, mixed> $array A row from the employees table
     */
    public static function fromDatabaseArray(array $array): self
    {
        return new self(
            id: (int) $array['id'],
            firstName: $array['first_name'],
            lastName: $array['last_name'],
            title: $array['title'],
            timezone: $array['timezone'],
            active: (bool) $array['active'],
            managerId: $array['manager_id'] !== null ? (int) $array['manager_id'] : null
        );
    }

    /**
     * Helper to get employee name in full format
     */
    public function getFullName(): string
    {
        return "{$this->firstName} {$this->lastName}";
    }
}
