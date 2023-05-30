<?php

namespace App\Service\Exchange\Action;

use App\Repository\RateRepository;

class ValidateCurrencyForExistAction
{
    /**
     * @var array|string[]
     */
    private array $currencies = [];

    public function __construct(protected RateRepository $rateRepository)
    {
        $this->currencies = $this->getAvailableCurrencies();
    }

    public function handle(string ...$rates): bool
    {
        foreach ($rates as $rate) {
            if (!in_array($rate, $this->currencies)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array<int, string>
     */
    private function getAvailableCurrencies(): array
    {
        // TODO: put in to Redis and get from them
        return $this->rateRepository->findAllAvailableCurrencies();
    }
}
