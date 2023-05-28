<?php

namespace App\Service\Import\Exception;

class WrongStatusCodeFromProviderException extends \Exception
{
    public function __construct(int $statusCode)
    {
        parent::__construct(sprintf('Unexpected status code from provider: %d', $statusCode));
    }
}
