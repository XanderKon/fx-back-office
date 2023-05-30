<?php

namespace App\Service\Import\Provider\Trait;

use App\Service\Import\Exception\ParseResponseFromProviderException;
use App\Service\Import\Exception\WrongStatusCodeFromProviderException;
use Symfony\Component\ErrorHandler\Error\FatalError;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait HasHttpClient
{
    private HttpClientInterface $httpClient;

    #[Required]
    public function setHttpClient(HttpClientInterface $httpClient): void
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @throws WrongStatusCodeFromProviderException
     * @throws ParseResponseFromProviderException
     */
    protected function makeRequest(string $url, string $method = 'GET'): string
    {
        try {
            $response = $this->httpClient->request($method, $url);

            if (200 !== $response->getStatusCode()) {
                throw new WrongStatusCodeFromProviderException($response->getStatusCode());
            }

            return $response->getContent();
        } catch (TransportExceptionInterface $e) {
            throw new FatalError('There is some network problem', $e->getCode(), $e->getTrace());
        } catch (HttpExceptionInterface $e) {
            throw new ParseResponseFromProviderException($e->getMessage());
        }
    }
}
