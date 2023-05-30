<?php

namespace App\Service\Exchange\Action;

use App\Enum\RedisEnum;
use App\Repository\RateRepository;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class ValidateCurrencyForExistAction
{
    /**
     * @var array|string[]
     */
    private array $currencies = [];

    public function __construct(
        protected readonly RateRepository $rateRepository, protected TagAwareCacheInterface $cache
    ) {
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
     *
     * @throws InvalidArgumentException
     *
     * @psalm-suppress ArgumentTypeCoercion
     */
    private function getAvailableCurrencies(): array
    {
        return $this->cache->get(RedisEnum::ALL_AVAILABLE_CURRENCIES->value, function (ItemInterface $item) {
            $item->tag(RedisEnum::TAG_INVALIDATE_BY_IMPORT->value);
            // expires in 1 hour
            $item->expiresAfter(3600);

            return $this->rateRepository->findAllAvailableCurrencies();
        });
    }
}
