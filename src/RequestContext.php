<?php

declare(strict_types=1);

namespace App;

class RequestContext
{
    /**
     * @param array<string, mixed> $params Query parameters, usually $_GET
     */
    public function __construct(
        private array $params
    ) {}

    /**
     * Returns the requested action, or null if it is missing or not a string
     * (e.g. ?action[]=manager arrives as an array)
     */
    public function getAction(): ?string
    {
        $action = $this->params['action'] ?? null;
        return is_string($action) ? $action : null;
    }

    /**
     * Returns the validated client provided employeeId, or null if it is
     * missing or not a positive integer
     */
    public function getEmployeeId(): ?int
    {
        // Positive integers only; anything else (0, -5, abc, arrays) becomes null
        return filter_var($this->params['employee_id'] ?? null, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
            'flags'   => FILTER_NULL_ON_FAILURE,
        ]);
    }
}
