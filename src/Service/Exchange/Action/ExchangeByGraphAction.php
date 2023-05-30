<?php

namespace App\Service\Exchange\Action;

use Fhaculty\Graph\Edge\Directed;
use Fhaculty\Graph\Set\Edges;

class ExchangeByGraphAction
{
    private const PRECISION = 4;

    public function handle(float $amount, Edges $edges): float
    {
        /** @var Directed $edge */
        foreach ($edges as $edge) {
            $amount *= floatval($edge->getWeight());
        }

        return round($amount, self::PRECISION);
    }
}
