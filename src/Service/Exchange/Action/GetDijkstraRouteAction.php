<?php

namespace App\Service\Exchange\Action;

use Fhaculty\Graph\Set\Edges;
use Fhaculty\Graph\Vertex;
use Graphp\Algorithms\ShortestPath\Dijkstra;

class GetDijkstraRouteAction
{
    public function handle(Vertex $vertexFrom, Vertex $vertexTo): Edges
    {
        $dijAlgo = new Dijkstra($vertexFrom);

        return $dijAlgo->getEdgesTo($vertexTo);
    }
}
