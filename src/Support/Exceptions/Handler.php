<?php

namespace PragmaRX\Tracker\Support\Exceptions;

use Exception;
use Error;
use Warning;
use Parse;
use Notice;
use CoreError;
use CoreWarning;
use CompileError;
use CompileWarning;
use UserError;
use UserWarning;
use UserNotice;
use Strict;
use RecoverableError;
use Deprecated;
use UserDeprecated;
use PragmaRX\Tracker\Tracker;

class Handler {

	private $tracker;

	private $originalHandler;

	public function __construct(Tracker $tracker)
	{
		$this->tracker = $tracker;

		$this->initializeHandlers();
	}

	private function initializeHandlers()
	{
		$this->originalExceptionHandler = set_exception_handler([$this, 'handleException']);

		$this->originalErrorHandler = set_error_handler([$this, 'handleError']);
	}

	private function handleException(Exception $exception)
	{
		try
		{
			$this->tracker->handleException($exception, $exception->getCode());
		}
		catch(\Exception $e)
		{
			// Ignore Tracker exceptions
		}

		// Call Laravel Exception Handler
		return call_user_func($this->originalHandler, $exception);
	}

	private function handleError($err_severity, $err_msg, $err_file, $err_line, array $err_context)
	{
		try
		{
			switch($err_severity)
			{
				case E_ERROR:               $error = new Error            ($err_msg, 0, $err_severity, $err_file, $err_line);
				case E_WARNING:             $error = new Warning          ($err_msg, 0, $err_severity, $err_file, $err_line);
				case E_PARSE:               $error = new Parse            ($err_msg, 0, $err_severity, $err_file, $err_line);
				case E_NOTICE:              $error = new Notice           ($err_msg, 0, $err_severity, $err_file, $err_line);
				case E_CORE_ERROR:          $error = new CoreError        ($err_msg, 0, $err_severity, $err_file, $err_line);
				case E_CORE_WARNING:        $error = new CoreWarning      ($err_msg, 0, $err_severity, $err_file, $err_line);
				case E_COMPILE_ERROR:       $error = new CompileError     ($err_msg, 0, $err_severity, $err_file, $err_line);
				case E_COMPILE_WARNING:     $error = new CompileWarning   ($err_msg, 0, $err_severity, $err_file, $err_line);
				case E_USER_ERROR:          $error = new UserError        ($err_msg, 0, $err_severity, $err_file, $err_line);
				case E_USER_WARNING:        $error = new UserWarning      ($err_msg, 0, $err_severity, $err_file, $err_line);
				case E_USER_NOTICE:         $error = new UserNotice       ($err_msg, 0, $err_severity, $err_file, $err_line);
				case E_STRICT:              $error = new Strict           ($err_msg, 0, $err_severity, $err_file, $err_line);
				case E_RECOVERABLE_ERROR:   $error = new RecoverableError ($err_msg, 0, $err_severity, $err_file, $err_line);
				case E_DEPRECATED:          $error = new Deprecated       ($err_msg, 0, $err_severity, $err_file, $err_line);
				case E_USER_DEPRECATED:     $error = new UserDeprecated   ($err_msg, 0, $err_severity, $err_file, $err_line);
				default:                    $error = new Error            ($err_msg, 0, $err_severity, $err_file, $err_line);
			}

			$this->tracker->handleException($error, $error->getCode());
		}
		catch(\Exception $e)
		{
			// Ignore Tracker exceptions
		}

		// Call Laravel Exception Handler
		return call_user_func($this->originalHandler, $err_severity, $err_msg, $err_file, $err_line);
	}

}
