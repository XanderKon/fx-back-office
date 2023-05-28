<?php

namespace App\Service\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class RateDTO
{
    #[Assert\NotBlank]
    private string $base;

    #[Assert\NotBlank]
    private string $target;

    #[Assert\NotBlank]
    private float $rate;

    public function getBase(): string
    {
        return $this->base;
    }

    public function setBase(string $base): void
    {
        $this->base = $base;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function setTarget(string $target): void
    {
        $this->target = $target;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function setRate(float $rate): void
    {
        $this->rate = $rate;
    }
}
