<?php

namespace App\Service\Import\Exception;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class UndefinedImportProviderException extends UnprocessableEntityHttpException
{
    public function __construct(string $sourceTitle)
    {
        parent::__construct(sprintf(
            'Undefined provider Source with title "%s". Make sure you create properly Source in database!', $sourceTitle
        ));
    }
}
