<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;
use PDOException;
use PDOStatement;
use App\Models\Employee;
use App\DTO\SubordinateResponse;
use App\Exceptions\DatabaseException;

class EmployeeRepository
{
    public function __construct(
        private PDO $pdo
    ) {}

    /**
     * Prepares and executes a query. On failure, wraps the PDOException in a
     * DatabaseException that names the operation, so the log shows which query failed.
     * @param array<string, mixed> $params
     * @param string $context Short description of the operation, e.g. "findById(id=1010)"
     * @throws DatabaseException
     */
    private function runQuery(string $sql, array $params, string $context): PDOStatement
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new DatabaseException("Query failed in EmployeeRepository::{$context}", 0, $e);
        }
    }

    /**
     * Retrieves an employee by their ID. Returns null if not found.
     */
    public function findById(int $id): ?Employee
    {
        $stmt = $this->runQuery(
            'SELECT * FROM employees WHERE id = :id',
            ['id' => $id],
            "findById(id={$id})"
        );

        $employeeData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($employeeData === false) {
            return null;
        }

        return Employee::fromDatabaseArray($employeeData);
    }

    /**
     * Get the direct manager of an employee, or null if they have none (e.g. the CEO).
     * The manager is returned even if inactive, so past data stays visible.
     */
    public function getManager(Employee $employee): ?Employee
    {
        if ($employee->managerId === null) {
            return null;
        }

        return $this->findById($employee->managerId);
    }

    /**
     * Get all active direct reports of an employee
     * @return Employee[]
     */
    public function getDirectReports(Employee $employee): array
    {
        $stmt = $this->runQuery(
            'SELECT * FROM employees WHERE manager_id = :manager_id AND active = 1 ORDER BY id',
            ['manager_id' => $employee->id],
            "getDirectReports(id={$employee->id})"
        );
        $employeesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(Employee::fromDatabaseArray(...), $employeesData);
    }

    /**
     * Fetches all active subordinates under an employee, each with their level in the tree
     * @return SubordinateResponse[]
     */
    public function getAllSubordinates(Employee $employee): array
    {
        $getSubordinatesQuery = <<<SQL
            WITH RECURSIVE employee_hierarchy AS (
                -- 1. Anchor Members: Select the direct reports to start the tree
                SELECT employees.*, 1 AS level
                FROM employees
                WHERE manager_id = :employee_id
                
                UNION ALL
                
                -- 2. Recursive Member: Find employees reporting to the previous level
                SELECT report.*, manager.level + 1
                FROM employees AS report
                INNER JOIN employee_hierarchy AS manager ON report.manager_id = manager.id
                -- 3. Limit the depth of recursion to avoid infinite loops in case of circular references
                WHERE manager.level < 100
            )
            -- 4. Final Query: Pull all subordinates sorted by organizational depth
            SELECT * 
            FROM employee_hierarchy
            -- 5. Filter out inactive employees at the final step. 
            --    This means that if a manager is inactive, their active subordinates will still be included.
            WHERE active = 1
            ORDER BY level, id;
        SQL;

        $stmt = $this->runQuery(
            $getSubordinatesQuery,
            ['employee_id' => $employee->id],
            "getAllSubordinates(id={$employee->id})"
        );
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map(SubordinateResponse::fromDatabaseRow(...), $rows);
    }
}
