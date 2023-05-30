<?php

namespace App\Service\Exchange\Action;

use App\Enum\RedisEnum;
use Fhaculty\Graph\Set\Edges;
use Fhaculty\Graph\Vertex;
use Graphp\Algorithms\ShortestPath\Dijkstra;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class GetDijkstraRouteAction
{
    public function __construct(protected TagAwareCacheInterface $cache)
    {
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     *
     * @psalm-suppress ArgumentTypeCoercion
     */
    public function handle(Vertex $vertexFrom, Vertex $vertexTo): Edges
    {
        $cacheKey = RedisEnum::DIJKSTRA_ROUTE->value."-{$vertexFrom->getId()}-{$vertexTo->getId()}";

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($vertexFrom, $vertexTo) {
            $item->tag(RedisEnum::TAG_INVALIDATE_BY_IMPORT->value);
            // expires in 1 hour
            $item->expiresAfter(3600);
            $dijAlgo = new Dijkstra($vertexFrom);

            return $dijAlgo->getEdgesTo($vertexTo);
        });
    }
}
