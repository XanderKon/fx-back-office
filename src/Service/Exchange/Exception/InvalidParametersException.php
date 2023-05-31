<?php

namespace App\Service\Exchange\Exception;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class InvalidParametersException extends UnprocessableEntityHttpException
{
    public function __construct(string $message = '')
    {
        parent::__construct($message);
    }
}
