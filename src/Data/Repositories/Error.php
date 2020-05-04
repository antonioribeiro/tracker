<?php

namespace PragmaRX\Tracker\Data\Repositories;

class Error extends Repository
{
    public function getMessageFromThrowable($throwable)
    {
        if ($message = $throwable->getMessage()) {
            return $message;
        }

        return $message;
    }

    public function getCodeFromThrowable($throwable)
    {
        if (method_exists($throwable, 'getCode') && $code = $throwable->getCode()) {
            return $code;
        }

        if (method_exists($throwable, 'getStatusCode') && $code = $throwable->getStatusCode()) {
            return $code;
        }
    }
}
