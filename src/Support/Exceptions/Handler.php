<?php

namespace PragmaRX\Tracker\Support\Exceptions;

use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler;
use PragmaRX\Tracker\Tracker;

class Handler implements ExceptionHandler
{
    private $tracker;

    private $illuminateHandler;

    private $originalExceptionHandler;

    private $originalErrorHandler;

    public function __construct(Tracker $tracker, $illuminateHandler = null)
    {
        $this->tracker = $tracker;

        $this->illuminateHandler = $illuminateHandler;

        $this->initializeHandlers();
    }

    private function initializeHandlers()
    {
        $this->originalExceptionHandler = set_exception_handler([$this, 'handleException']);

        $this->originalErrorHandler = set_error_handler([$this, 'handleError']);
    }

    public function handleException(Exception $exception)
    {
        try {
            $this->tracker->handleException($exception, $exception->getCode());
        } catch (\Exception $e) {
            // Ignore Tracker exceptions
        }

        // Call Laravel Exception Handler
        return call_user_func($this->originalExceptionHandler, $exception);
    }

    public function handleError($err_severity, $err_msg, $err_file, $err_line, array $err_context)
    {
        try {
            $error = ExceptionFactory::make($err_severity, $err_msg);

            $this->tracker->handleException($error, $error->getCode());
        } catch (\Exception $e) {
            // Ignore Tracker exceptions
        }

        // Call Laravel Exception Handler
        return call_user_func($this->originalErrorHandler, $err_severity, $err_msg, $err_file, $err_line);
    }

    public function report(Exception $e)
    {
        try {
            $this->tracker->handleException($e);
        } catch (Exception $exception) {
            // ignore
        }

        $this->illuminateHandler->report($e);
    }

    public function render($request, Exception $e)
    {
        return $this->illuminateHandler->render($request, $e);
    }

    public function renderForConsole($output, Exception $e)
    {
        return $this->illuminateHandler->renderForConsole($output, $e);
    }
}
