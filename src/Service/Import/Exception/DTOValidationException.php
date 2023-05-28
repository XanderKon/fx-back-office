<?php

namespace App\Service\Import\Exception;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class DTOValidationException extends UnprocessableEntityHttpException
{
    public function __construct(string $errors)
    {
        parent::__construct(sprintf(
            'There is some problem with DTO validation: %s', $errors
        ));
    }
}
