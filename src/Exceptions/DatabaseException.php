<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when a database operation fails. The message says which operation
 * failed (for logs only); the original PDOException is kept as "previous".
 */
class DatabaseException extends RuntimeException
{
}
