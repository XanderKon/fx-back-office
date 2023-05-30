<?php

namespace App\Service\Import\Provider;

use App\Service\DTO\RatesDTO;
use App\Service\Import\Exception\BadXMLResponseException;
use App\Service\Import\Exception\ParseResponseFromProviderException;
use App\Service\Import\Exception\WrongStatusCodeFromProviderException;
use App\Service\Import\Provider\Trait\HasHttpClient;
use App\Service\Import\Provider\Trait\HasParserToDTO;

final class EcbProvider implements ProviderInterface
{
    use HasHttpClient;
    use HasParserToDTO;

    private const PROVIDER_NAME = 'ecb';

    private string $targetUrl = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

    private string $response = '';

    public function __construct()
    {
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

        return $this->createDTO((string) json_encode($response));
    }
}
