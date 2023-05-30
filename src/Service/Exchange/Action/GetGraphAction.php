<?php

namespace App\Service\Exchange\Action;

use App\Repository\RateRepository;
use Fhaculty\Graph\Graph;

class GetGraphAction
{
    public function __construct(private RateRepository $rateRepository)
    {
    }

    public function handle(): Graph
    {
        // TODO: try to get it from Redis
        return $this->buildGraph();
    }

    private function buildGraph(): Graph
    {
        $currencies = $this->rateRepository->findAllRateWithActiveSource();

        $graph = new Graph();

        foreach ($currencies as $currency) {
            /**
             * @psalm-suppress InvalidArgument
             *
             * @phpstan-ignore-next-line
             */
            $vertex1 = $graph->createVertex($currency['base'], true);
            /**
             * @psalm-suppress InvalidArgument
             *
             * @phpstan-ignore-next-line
             */
            $vertex2 = $graph->createVertex($currency['target'], true);

            // Casting to float
            $rate = floatval($currency['rate']);

            // Build route (directional)
            $edge1 = $vertex1->createEdgeTo($vertex2);
            $edge1->setWeight($rate);
            $edge1->setAttribute('source', $currency['title']);

            // Build reverse route (directional)
            $edge2 = $vertex2->createEdgeTo($vertex1);
            $edge2->setWeight(1 / $rate);
            $edge2->setAttribute('source', $currency['title']);
        }

        return $graph;
    }
}
