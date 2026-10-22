<?php

declare(strict_types=1);

namespace App\DTO;

use App\Models\Employee;

readonly class SubordinateResponse
{
    public function __construct(
        public Employee $employee,
        public int $level
    ) {}

    /**
     * Creates a DTO instance directly from a raw database row array.
     * @param array<string, mixed> $row A row from the employees table plus the
     *                                  `level` column added by the recursive query
     */
    public static function fromDatabaseRow(array $row): self
    {
        return new self(
            Employee::fromDatabaseArray($row),
            (int) $row['level']
        );
    }
}
