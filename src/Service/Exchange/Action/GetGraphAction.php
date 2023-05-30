<?php

namespace App\Service\Exchange\Action;

use App\Enum\RedisEnum;
use App\Repository\RateRepository;
use Fhaculty\Graph\Graph;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class GetGraphAction
{
    public function __construct(
        private readonly RateRepository $rateRepository,
        protected TagAwareCacheInterface $cache
    ) {
    }

    /**
     * @throws InvalidArgumentException
     *
     * @psalm-suppress ArgumentTypeCoercion
     */
    public function handle(): Graph
    {
        return $this->cache->get(RedisEnum::GRAPH->value, function (ItemInterface $item) {
            $item->tag(RedisEnum::TAG_INVALIDATE_BY_IMPORT->value);
            // expires in 1 hour
            $item->expiresAfter(3600);

            return $this->buildGraph();
        });
    }

    /**
     * @psalm-suppress InvalidArgument
     */
    private function buildGraph(): Graph
    {
        $currencies = $this->rateRepository->findAllRateWithActiveSource();

        $graph = new Graph();

        foreach ($currencies as $currency) {
            /**
             * @phpstan-ignore-next-line
             */
            $vertex1 = $graph->createVertex($currency['base'], true);
            /**
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
