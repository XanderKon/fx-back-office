<?php

namespace App\Service\Import\Exception;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class BadXMLResponseException extends UnprocessableEntityHttpException
{
    public function __construct(string $message = '')
    {
        parent::__construct(sprintf(
            'Cannot parse xml with error: "%s"',
            $message
        ));
    }
}
