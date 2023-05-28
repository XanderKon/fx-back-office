<?php

namespace App\Service\Import\Exception;

class ParseResponseFromProviderException extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct(sprintf('There is some problem with parsing response from provider" : "%s".',
            $message
        ));
    }
}
