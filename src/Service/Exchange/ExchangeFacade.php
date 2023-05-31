<?php

namespace App\Service\Exchange;

use App\Service\Exchange\Action\ExchangeByGraphAction;
use App\Service\Exchange\Action\GetDijkstraRouteAction;
use App\Service\Exchange\Action\GetGraphAction;
use App\Service\Exchange\Action\ValidateCurrencyForExistAction;
use App\Service\Exchange\Exception\InvalidParametersException;
use Psr\Cache\InvalidArgumentException;

class ExchangeFacade
{
    public function __construct(
        private readonly GetGraphAction $getGraphAction,
        private readonly GetDijkstraRouteAction $dijkstraRouteAction,
        private readonly ExchangeByGraphAction $exchangeByGraphAction,
        private readonly ValidateCurrencyForExistAction $validateCurrencyForExistAction,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function handle(float $amount, string $from, string $to): float
    {
        $from = mb_strtoupper($from);
        $to = mb_strtoupper($to);

        if (!$this->validateCurrencyForExistAction->handle($from, $to)) {
            throw new InvalidParametersException(sprintf('One or all currencies from your request [%s, %s] does not exist in our system. Sorry', $from, $to));
        }

        $graph = $this->getGraphAction->handle();

        $vertexFrom = $graph->getVertex($from);
        $vertexTo = $graph->getVertex($to);

        $edges = $this->dijkstraRouteAction->handle($vertexFrom, $vertexTo);

        return $this->exchangeByGraphAction->handle($amount, $edges);
    }
}
