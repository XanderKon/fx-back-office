<?php

namespace App\Tests;

use App\Repository\RateRepository;
use App\Service\Exchange\Action\ExchangeByGraphAction;
use App\Service\Exchange\Action\GetGraphAction;
use Fhaculty\Graph\Edge\Directed;
use Fhaculty\Graph\Graph;
use Graphp\Algorithms\ShortestPath\BreadthFirst;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ExchangeServiceTest extends KernelTestCase
{
    protected ContainerInterface $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = static::getContainer();

        $rateRepositoryMock = $this->createMock(RateRepository::class);
        $rateRepositoryMock
            ->method('findAllRateWithActiveSource')
            ->willReturn([
                [
                    'base' => 'EUR',
                    'target' => 'USD',
                    'rate' => 1.222,
                    'title' => 'ecb'
                ],
                [
                    'base' => 'EUR',
                    'target' => 'GBP',
                    'rate' => 0.982,
                    'title' => 'ecb'
                ],
            ]);

        $this->container->set(RateRepository::class, $rateRepositoryMock);
    }

    public function testGetGraphAction(): void
    {
        $getGraphAction = $this->container->get(GetGraphAction::class);

        /** @var Graph $graph */
        $graph = $getGraphAction->handle();

        $this->assertEquals('EUR', $graph->getVertex('EUR')->getId());

        /** @var Directed $edge */
        foreach ($graph->getEdges() as $edge) {
            if ($edge->getVertexStart()->getId() === 'EUR' && $edge->getVertexEnd()->getId() === 'USD') {
                $this->assertEquals(1.222, $edge->getWeight());
            }
        }
    }

    public function testExchangeByGraphAction(): void
    {
        $exchangeByGraphAction = $this->container->get(ExchangeByGraphAction::class);

        $getGraphAction = $this->container->get(GetGraphAction::class);

        /** @var Graph $graph */
        $graph = $getGraphAction->handle();

        $bfsAlgo = new BreadthFirst($graph->getVertex('EUR'));
        $edges = $bfsAlgo->getEdgesTo($graph->getVertex('USD'));

        $amount = $exchangeByGraphAction->handle(2, $edges);
        $this->assertEquals(2.444, $amount);

        // And reverse
        $bfsAlgo = new BreadthFirst($graph->getVertex('USD'));
        $edges = $bfsAlgo->getEdgesTo($graph->getVertex('EUR'));

        $amount = $exchangeByGraphAction->handle(4, $edges);
        $this->assertEquals(3.2733, floatval(number_format($amount, 4)));
    }
}
