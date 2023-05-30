<?php

namespace App\Service\Exchange;

use App\Service\Exchange\Action\ExchangeByGraphAction;
use App\Service\Exchange\Action\GetDijkstraRouteAction;
use App\Service\Exchange\Action\GetGraphAction;

class ExchangeFacade
{
    public function __construct(
        private readonly GetGraphAction $getGraphAction,
        private readonly GetDijkstraRouteAction $dijkstraRouteAction,
        private readonly ExchangeByGraphAction $exchangeByGraphAction,
    ) {
    }

    public function handle(float $amount, string $from, string $to): float
    {
        $graph = $this->getGraphAction->handle();

        $vertexFrom = $graph->getVertex($from);
        $vertexTo = $graph->getVertex($to);

        $edges = $this->dijkstraRouteAction->handle($vertexFrom, $vertexTo);

        return $this->exchangeByGraphAction->handle($amount, $edges);
    }
}
