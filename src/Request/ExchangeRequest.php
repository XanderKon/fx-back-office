<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class ExchangeRequest
{
    public function __construct(
        #[Assert\NotBlank]
        public string $from,

        #[Assert\NotBlank]
        public string $to,

        #[Assert\NotBlank]
        #[Assert\PositiveOrZero]
        public float $amount,
    ) {
    }
}
