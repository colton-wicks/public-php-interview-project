<?php

declare(strict_types=1);

namespace App\Actions;

use App\RequestContext;
use App\Models\Employee;
use App\Repositories\EmployeeRepository;

abstract class BaseEmployeeAction implements ActionHandlerInterface
{
    public function __construct(
        protected EmployeeRepository $employeeRepository
    ) {}

    /**
     * Shared steps for every employee action: validate employee_id (400),
     * look the employee up (404), then run the specific action.
     */
    public function handle(RequestContext $requestContext): void
    {
        $employeeId = $requestContext->getEmployeeId();
        if ($employeeId === null) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'A valid positive integer "employee_id" is required for this action.']);
            exit;
        }

        $employee = $this->employeeRepository->findById($employeeId);
        if ($employee === null) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Employee not found.']);
            exit;
        }

        header('Content-Type: text/plain; charset=utf-8');
        $this->execute($requestContext, $employee);
    }

    /**
     * Runs the action for an employee who has already been validated and found.
     * Output is plain text.
     */
    abstract protected function execute(RequestContext $requestContext, Employee $employee): void;
}
