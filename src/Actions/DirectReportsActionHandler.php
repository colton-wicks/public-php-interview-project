<?php

declare(strict_types=1);

namespace App\Actions;

use App\RequestContext;
use App\Models\Employee;

class DirectReportsActionHandler extends BaseEmployeeAction
{
    protected function execute(RequestContext $requestContext, Employee $employee): void
    {
        $directReports = $this->employeeRepository->getDirectReports($employee);
        $this->output($employee, $directReports);
    }

    /**
     * Prints the employee's direct reports on one line, or a message if there are none
     * @param Employee[] $directReports
     */
    private function output(Employee $employee, array $directReports): void
    {
        if (empty($directReports)) {
            echo "Direct reports of {$employee->getFullName()}: No direct reports found" . PHP_EOL;
            return;
        }

        $employeeNames = array_map(fn(Employee $report) => $report->getFullName(), $directReports);
        echo "Direct reports of {$employee->getFullName()}: " . implode(', ', $employeeNames) . PHP_EOL;
    }
}
