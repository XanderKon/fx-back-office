<?php

namespace App\Service\Import\Providers;

use App\Service\DTO\RatesDTO;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.provider')]
interface ProviderInterface
{
    public function getProviderName(): string;

    public function getData(): self;

    public function parseData(): RatesDTO;
}
