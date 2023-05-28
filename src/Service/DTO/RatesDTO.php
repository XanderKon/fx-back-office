<?php

namespace App\Service\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class RatesDTO
{
    /**
     * @var array<RateDTO>
     */
    #[Assert\Valid]
    private array $values;

    /**
     * @return RateDTO[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param array<RateDTO> $values
     */
    public function setValues(array $values): void
    {
        $this->values = $values;
    }
}
