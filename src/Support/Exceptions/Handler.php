<?php

namespace PragmaRX\Tracker\Support\Exceptions;

use Illuminate\Contracts\Debug\ExceptionHandler;
use PragmaRX\Tracker\Tracker;
use Throwable;

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
        $this->originalExceptionHandler = set_exception_handler([$this, 'handleThrowable']);

        $this->originalErrorHandler = set_error_handler([$this, 'handleError']);
    }

    public function handleThrowable(Throwable $throwable)
    {
        try {
            $this->tracker->handleThrowable($throwable);
        } catch (\Exception $e) {
            // Ignore Tracker exceptions
        }

        // Call Laravel Exception Handler
        return call_user_func($this->originalExceptionHandler, $throwable);
    }

    public function handleError($err_severity, $err_msg, $err_file, $err_line, array $err_context)
    {
        try {
            $error = ExceptionFactory::make($err_severity, $err_msg);

            $this->tracker->handleThrowable($error);
        } catch (\Exception $e) {
            // Ignore Tracker exceptions
        }

        // Call Laravel Exception Handler
        return call_user_func($this->originalErrorHandler, $err_severity, $err_msg, $err_file, $err_line);
    }

    public function report(Throwable $e)
    {
        try {
            $this->tracker->handleThrowable($e);
        } catch (Exception $exception) {
            // ignore
        }

        $this->illuminateHandler->report($e);
    }

    public function shouldReport(Throwable $e)
    {
        return $this->illuminateHandler->shouldReport($e);
    }

    public function render($request, Throwable $e)
    {
        return $this->illuminateHandler->render($request, $e);
    }

    public function renderForConsole($output, Throwable $e)
    {
        return $this->illuminateHandler->renderForConsole($output, $e);
    }
}
