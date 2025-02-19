<?php

namespace App\Http\Controllers;

use Exception;

abstract class Controller
{
    /**
     * Create a log with a default context.
     *
     * @param string $title
     * @param Exception $e
     * @return void
     */
    protected function handleDefaultExceptionLog(string $title, Exception $e): void
    {
        logService()->withContext($title, [
            'code' => $e->getCode(),
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
    }
}
