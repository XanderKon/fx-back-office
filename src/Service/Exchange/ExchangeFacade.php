<?php

namespace App\Service\Exchange;

use App\Service\Exchange\Action\ExchangeByGraphAction;
use App\Service\Exchange\Action\GetDijkstraRouteAction;
use App\Service\Exchange\Action\GetGraphAction;
use App\Service\Exchange\Action\ValidateCurrencyForExistAction;
use App\Service\Exchange\Exception\InvalidParametersException;
use App\Service\Exchange\Response\ExchangeResponse;
use Fhaculty\Graph\Edge\Directed;
use Fhaculty\Graph\Set\Edges;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Serializer\SerializerInterface;

class ExchangeFacade
{
    public function __construct(
        private readonly GetGraphAction $getGraphAction,
        private readonly GetDijkstraRouteAction $dijkstraRouteAction,
        private readonly ExchangeByGraphAction $exchangeByGraphAction,
        private readonly ValidateCurrencyForExistAction $validateCurrencyForExistAction,
        private readonly SerializerInterface $serializer
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function handle(float $amount, string $from, string $to): ExchangeResponse
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

        $amount = $this->exchangeByGraphAction->handle($amount, $edges);

        return $this->createResponse($amount, $from, $to, $edges);
    }

    private function createResponse(float $amount, string $from, string $to, Edges $edges): ExchangeResponse
    {
        $response = [
            'amount' => $amount,
            'from' => $from,
            'to' => $to,
        ];

        /** @var Directed $edge */
        foreach ($edges as $edge) {
            $response['route'][] = [
                'from' => $edge->getVertexStart()->getId(),
                'to' => $edge->getVertexEnd()->getId(),
                'rate' => $edge->getWeight(),
            ];
        }

        return $this->serializer->deserialize((string) json_encode($response), ExchangeResponse::class, 'json');
    }
}
