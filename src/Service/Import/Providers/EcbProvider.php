<?php

namespace App\Service\Import\Providers;

use App\Service\DTO\RatesDTO;
use App\Service\Import\Exception\BadXMLResponseException;
use App\Service\Import\Exception\DTOValidationException;
use App\Service\Import\Exception\ParseResponseFromProviderException;
use App\Service\Import\Exception\WrongStatusCodeFromProviderException;
use App\Service\Import\Providers\Traits\HasHttpClient;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class EcbProvider implements ProviderInterface
{
    use HasHttpClient;

    private const PROVIDER_NAME = 'ecb';

    private string $targetUrl = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

    private string $response = '';

    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function getProviderName(): string
    {
        return self::PROVIDER_NAME;
    }

    /**
     * @throws WrongStatusCodeFromProviderException
     * @throws ParseResponseFromProviderException
     */
    public function getData(): self
    {
        $this->response = $this->makeRequest($this->targetUrl);

        return $this;
    }

    public function parseData(): RatesDTO
    {
        $xml = simplexml_load_string($this->response);

        if (!$xml) {
            throw new BadXMLResponseException();
        }

        $response = [];
        foreach ((array) $xml->xpath('//*[@currency]') as $element) {
            if (!$element) {
                continue;
            }

            $attributes = $element->attributes();
            if (!$attributes) {
                continue;
            }

            $response['values'][] = [
                'base' => 'EUR',
                'target' => (string) $attributes->currency,
                'rate' => floatval($attributes->rate),
            ];
        }

        $dto = $this->serializer->deserialize((string) json_encode($response), RatesDTO::class, 'json');
        $violations = $this->validator->validate($dto);

        if (0 !== count($violations)) {
            $messages = [];
            foreach ($violations as $error) {
                $messages[$error->getPropertyPath()][] = $error->getMessage();
            }
            throw new DTOValidationException((string) json_encode($messages));
        }

        return $dto;
    }
}
