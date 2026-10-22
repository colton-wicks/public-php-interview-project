<?php

declare(strict_types=1);

/**
 * Registers an autoloader for the App\ namespace.
 *
 * spl_autoload_register() takes a callback that PHP calls automatically the first
 * time code uses a class that hasn't been loaded yet (e.g. `new EmployeeRepository()`).
 * PHP passes the fully qualified class name, such as "App\Repositories\EmployeeRepository",
 * and the callback's job is to require the file that defines it.
 *
 * The callback is an anonymous function (closure). It's declared `static` because it
 * never uses $this, so PHP doesn't bind it to an object. That's a small optimisation
 * and makes the intent clear: this function depends only on its argument.
 */
spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';

    // Folder that the App\ namespace maps to (PSR-4 style: namespace prefix => base directory)
    $baseDir = __DIR__ . '/src/';

    // Only handle our own namespace; return early so other autoloaders (if any) can try
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    // Strip the prefix: "App\Repositories\EmployeeRepository" => "Repositories\EmployeeRepository"
    $relativeClass = substr($class, strlen($prefix));

    // Turn namespace separators into folder separators and add .php:
    // "Repositories\EmployeeRepository" => "src/Repositories/EmployeeRepository.php"
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    // Load the file if it exists. If it doesn't, do nothing: PHP then reports
    // "Class not found", which is clearer than a failed require
    if (file_exists($file)) {
        require_once $file;
    }
});
