<?php

declare(strict_types=1);

namespace App\Actions;

use App\RequestContext;
use App\Models\Employee;

class ManagerActionHandler extends BaseEmployeeAction
{
    protected function execute(RequestContext $requestContext, Employee $employee): void
    {
        $manager = $this->employeeRepository->getManager($employee);
        $this->output($employee, $manager);
    }

    /**
     * Prints the employee's manager, or a message if they have none (e.g. the CEO)
     */
    private function output(Employee $employee, ?Employee $manager): void
    {
        if ($manager === null) {
            echo "Manager of {$employee->getFullName()}: No manager found" . PHP_EOL;
            return;
        }
        echo "Manager of {$employee->getFullName()}: {$manager->getFullName()}" . PHP_EOL;
    }
}
