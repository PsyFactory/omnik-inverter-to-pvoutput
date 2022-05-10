<?php

namespace PsyPvoutput;

use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\GuzzleException;
use Exception;
use InvalidArgumentException;

/**
 * PsyFactory PvOutput client
 */
class Client
{
    protected string $systemId;
    protected string $apiKey;

    const BASE_URL = 'https://pvoutput.org';
    const ADDSTATUS_URL = '/service/r2/addstatus.jsp';

    /**
     * Create PvOutput client
     * @param string $systemId
     * @param string $apiKey
     */
    public function __construct(string $systemId, string $apiKey)
    {
        $this->systemId = $systemId;
        $this->apiKey = $apiKey;
    }

    /**
     * Send a status to PvOutput
     *
     * @param Status $status
     * @return void
     * @throws Exception|GuzzleException
     */
    public function addStatus(Status $status): void
    {
        $this->sendRequest(self::ADDSTATUS_URL, 'POST', $status->toArray());
    }

    /**
     * Send a request to PVoutput
     * @param string $url
     * @param string $method
     * @param array|null $data
     * @return string
     * @throws Exception|InvalidArgumentException|GuzzleException
     */
    private function sendRequest($url, string $method, array $data = null): string
    {
        if (!in_array($method, ['POST', 'GET'])) {
            throw new InvalidArgumentException(__METHOD__ . '; Invalid method');
        }

        // Create HTTP client
        $httpClient = new GuzzleHttpClient([
            'base_uri' => self::BASE_URL,
            'headers' => [
                'X-Pvoutput-Apikey' => $this->apiKey,
                'X-Pvoutput-SystemId' => $this->systemId
            ]
        ]);

        // Determine request options
        $requestOptions = [];

        // Add data to request
        if (is_array($data) && count($data) > 0) {
            if ($method === 'POST') {
                $requestOptions['form_params'] = $data;
            } else {
                $url .= '?' . http_build_query($data);
            }
        }

        // Send status request
        $response = $httpClient->request($method, $url, $requestOptions);

        // Check response code
        if ($response->getStatusCode() !== 200) {
            throw new Exception(__METHOD__ . '; PVoutput API returned an invalid response code: ' . $response->getStatusCode() . ', body: ' . $response->getBody()->getContents());
        }

        return $response->getBody()->getContents();
    }
}
