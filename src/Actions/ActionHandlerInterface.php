<?php

declare(strict_types=1);

namespace App\Actions;

use App\RequestContext;

interface ActionHandlerInterface
{
    /**
     * Executes the specific database or business logic action
     */
    public function handle(RequestContext $requestContext): void;
}
