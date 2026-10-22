<?php

declare(strict_types=1);

namespace App;

use App\Actions\ActionHandlerInterface;

class ActionRouter
{
    /**
     * List of registered action routes and their corresponding handlers.
     * @var array<string, ActionHandlerInterface>
     */
    private array $routes = [];

    /**
     * Register a valid action string to its corresponding class handler.
     * Action names are case-insensitive, so they are stored lowercase.
     * @throws \InvalidArgumentException If the action name is already registered
     */
    public function register(string $actionName, ActionHandlerInterface $handler): self
    {
        $actionName = strtolower($actionName);

        // Check if the route action already exists, throw an error if it does
        if (isset($this->routes[$actionName])) {
            throw new \InvalidArgumentException("Action '$actionName' is already registered.");
        }
        $this->routes[$actionName] = $handler;
        return $this;
    }

    /**
     * Runs the handler for the requested action; sends a 400 and exits if the
     * action is missing or unknown
     */
    public function dispatch(?string $actionName, RequestContext $requestContext): void
    {
        if ($actionName === null || $actionName === '') {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Action parameter is required.']);
            exit;
        }

        // Strict security whitelist check (case-insensitive: "Manager" and "MANAGER" both work)
        $handler = $this->routes[strtolower($actionName)] ?? null;
        if ($handler === null) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => "Action '$actionName' not found."]);
            exit;
        }

        // Execute the single responsibility class handler
        $handler->handle($requestContext);
    }
}
