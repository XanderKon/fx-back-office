<?php

namespace App\Service\Import\Provider;

use App\Service\DTO\RatesDTO;
use App\Service\Import\Exception\ParseResponseFromProviderException;
use App\Service\Import\Exception\WrongStatusCodeFromProviderException;
use App\Service\Import\Provider\Trait\HasHttpClient;
use App\Service\Import\Provider\Trait\HasParserToDTO;

final class CoindeskProvider implements ProviderInterface
{
    use HasHttpClient;
    use HasParserToDTO;

    private const PROVIDER_NAME = 'coindesk';

    private string $targetUrl = 'https://api.coindesk.com/v1/bpi/currentprice.json';

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
        $response = json_decode($this->response, true);
        if (empty($response) || !isset($response['bpi'])) {
            throw new ParseResponseFromProviderException('There is no valid data to parse');
        }

        $res = [];
        /**
         * @var mixed                 $target
         * @var array<string, string> $data
         */
        foreach ($response['bpi'] as $target => $data) {
            $res['values'][] = [
                'base' => 'BTC',
                'target' => trim((string) $target),
                'rate' => floatval($data['rate_float']),
            ];
        }

        return $this->createDTO((string) json_encode($res));
    }
}
