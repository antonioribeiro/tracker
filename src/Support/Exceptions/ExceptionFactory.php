<?php

namespace PragmaRX\Tracker\Support\Exceptions;

class ExceptionFactory
{
    public static function make($errorCode, $errorMessage)
    {
        switch ($errorCode) {
            case E_ERROR:               return new Error($errorMessage, $errorCode);
            case E_WARNING:             return new Warning($errorMessage, $errorCode);
            case E_PARSE:               return new Parse($errorMessage, $errorCode);
            case E_NOTICE:              return new Notice($errorMessage, $errorCode);
            case E_CORE_ERROR:          return new CoreError($errorMessage, $errorCode);
            case E_CORE_WARNING:        return new CoreWarning($errorMessage, $errorCode);
            case E_COMPILE_ERROR:       return new CompileError($errorMessage, $errorCode);
            case E_COMPILE_WARNING:     return new CompileWarning($errorMessage, $errorCode);
            case E_USER_ERROR:          return new UserError($errorMessage, $errorCode);
            case E_USER_WARNING:        return new UserWarning($errorMessage, $errorCode);
            case E_USER_NOTICE:         return new UserNotice($errorMessage, $errorCode);
            case E_STRICT:              return new Strict($errorMessage, $errorCode);
            case E_RECOVERABLE_ERROR:   return new RecoverableError($errorMessage, $errorCode);
            case E_DEPRECATED:          return new Deprecated($errorMessage, $errorCode);
            case E_USER_DEPRECATED:     return new UserDeprecated($errorMessage, $errorCode);
        }

        return new Error($errorMessage, $errorCode);
    }
}
