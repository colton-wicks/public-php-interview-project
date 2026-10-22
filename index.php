<?php

declare(strict_types=1);

require __DIR__ . '/autoload.php';

use App\Database;
use App\ActionRouter;
use App\RequestContext;
use App\Repositories\EmployeeRepository;
use App\Actions\{ManagerActionHandler, DirectReportsActionHandler, EveryoneBelowActionHandler};
use App\Exceptions\DatabaseException;

// Last-resort handler for anything not caught closer to where it happened.
// Logs the full exception (message, previous PDOException, stack trace) server-side
// and returns a generic 500 so no SQL or connection details reach the client.
set_exception_handler(static function (Throwable $e): void {
    error_log((string) $e);

    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json');
    }

    echo json_encode(['error' => $e instanceof DatabaseException
        ? 'A database error occurred.'
        : 'An unexpected error occurred.']);
});

$employeeRepository = new EmployeeRepository(Database::getConnection());

// Register all valid action routes and their corresponding handlers
// Note: If this list grows, we would eventually want to switch to discovering handlers
// from src/Actions (e.g. with an #[Action] attribute) and creating only the one requested
$router = new ActionRouter()
    ->register('manager', new ManagerActionHandler($employeeRepository))
    ->register('direct_reports', new DirectReportsActionHandler($employeeRepository))
    ->register('everyone_below', new EveryoneBelowActionHandler($employeeRepository));

// Get query parameters (e.g., index.php?employee_id=1002&action=manager)
$requestContext = new RequestContext($_GET);

$router->dispatch($requestContext->getAction(), $requestContext);
