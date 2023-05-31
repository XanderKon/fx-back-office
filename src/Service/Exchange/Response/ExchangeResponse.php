<?php

namespace App\Service\Exchange\Response;

class ExchangeResponse
{
    private float $amount;

    private string $from;

    private string $to;

    /**
     * @var array<string, mixed>
     */
    private array $route;

    public function __construct()
    {
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function setFrom(string $from): void
    {
        $this->from = $from;
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function setTo(string $to): void
    {
        $this->to = $to;
    }

    /**
     * @return array<string, mixed>
     */
    public function getRoute(): array
    {
        return $this->route;
    }

    /**
     * @param array<string, mixed> $route
     */
    public function setRoute(array $route): void
    {
        $this->route = $route;
    }
}
