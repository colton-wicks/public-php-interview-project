<?php

declare(strict_types=1);

namespace App\Actions;

use App\RequestContext;
use App\Models\Employee;
use App\DTO\SubordinateResponse;

class EveryoneBelowActionHandler extends BaseEmployeeAction
{
    protected function execute(RequestContext $requestContext, Employee $employee): void
    {
        $subordinates = $this->employeeRepository->getAllSubordinates($employee);
        $this->output($employee, $subordinates);
    }

    /**
     * Prints everyone below the employee, one line per level, or a message if there is no one
     * @param SubordinateResponse[] $subordinates
     */
    private function output(Employee $employee, array $subordinates): void
    {
        echo "Everyone below {$employee->getFullName()}:" . PHP_EOL;
        if (empty($subordinates)) {
            echo 'No subordinates found for this employee.' . PHP_EOL;
            return;
        }

        // Group names by level; the query already returns rows ordered by level, then id
        $employeeNamesByLevel = [];
        foreach ($subordinates as $subordinate) {
            $employeeNamesByLevel[$subordinate->level][] = $subordinate->employee->getFullName();
        }

        foreach ($employeeNamesByLevel as $level => $names) {
            echo implode(', ', $names) . " (Level {$level})" . PHP_EOL;
        }
    }
}
