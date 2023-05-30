<?php

namespace App\Service\Exchange;

use App\Service\Exchange\Action\ExchangeByGraphAction;
use App\Service\Exchange\Action\GetDijkstraRouteAction;
use App\Service\Exchange\Action\GetGraphAction;
use Psr\Cache\InvalidArgumentException;

class ExchangeFacade
{
    public function __construct(
        private readonly GetGraphAction $getGraphAction,
        private readonly GetDijkstraRouteAction $dijkstraRouteAction,
        private readonly ExchangeByGraphAction $exchangeByGraphAction,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function handle(float $amount, string $from, string $to): float
    {
        $graph = $this->getGraphAction->handle();

        $vertexFrom = $graph->getVertex($from);
        $vertexTo = $graph->getVertex($to);

        $edges = $this->dijkstraRouteAction->handle($vertexFrom, $vertexTo);

        return $this->exchangeByGraphAction->handle($amount, $edges);
    }
}
