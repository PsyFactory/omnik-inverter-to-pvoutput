<?php

namespace PsyOmnik;

use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Exception;

/**
 * PsyFactory Omnik inverter client
 */
class Client
{
    protected string $ip;
    protected string $username;
    protected string $password;

    /**
     * Constructor
     * @param string $ip
     * @param string $username
     * @param string $password
     */
    public function __construct(string $ip, string $username, string $password)
    {
        $this->setIp($ip);
        $this->setUsername($username);
        $this->setPassword($password);
    }

    /**
     * Set the IP address to use
     * @param string $ip
     * @return void
     * @throws InvalidArgumentException
     */
    public function setIp(string $ip): void
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new InvalidArgumentException(__METHOD__ . '; Invalid ip address');
        }

        $this->ip = $ip;
    }

    /**
     * Set the username
     * @param string $username
     * @return void
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * Set the password
     * @param string $password
     * @return void
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * Retrieve current inverter status
     * @return Status
     * @throws Exception|GuzzleException
     */
    public function retrieveStatus(): Status
    {
        // Create HTTP client
        $httpClient = new GuzzleHttpClient([
            'base_uri' => 'http://' . $this->ip
        ]);

        // Send status request
        $response = $httpClient->request('GET', '/js/status.js', [
            'auth' => [$this->username, $this->password]
        ]);

        // Check response code
        if ($response->getStatusCode() !== 200) {
            throw new Exception(__METHOD__ . '; Inverter returned an invalid response code: ' . $response->getStatusCode());
        }

        // Get response txt
        $responseTxt = $response->getBody()->getContents();

        // Try the different configs
        foreach ($this->getSearchOptions() as $searchKey => $options) {
            $status = $this->convertResponse($responseTxt, $searchKey, $options);
            if (!is_null($status)) {
                return $status;
            }
        }

        throw new Exception(__METHOD__ . '; Failed to handle the status response');
    }

    /**
     * Convert response from convertor
     * @param string $responseTxt
     * @param string $searchKey
     * @param array $options
     * @return Status|void
     * @throws Exception
     */
    private function convertResponse(string $responseTxt, string $searchKey, array $options)
    {
        // Check if response txt contains the specified key
        $pos = strpos($responseTxt, $searchKey);
        if ($pos === false) {
            return;
        }

        // Check if response text also contains a closing string
        $startPos = $pos + strlen($searchKey);
        $endPos = strpos($responseTxt, '"', $startPos);
        if ($endPos === false) {
            return;
        }

        // Retrieve info
        $info = explode(',', substr($responseTxt, $startPos, $endPos - $startPos));
        $currentWattOptions = $options['currentW'];
        $totalKwhOptions = $options['totalkWh'];
        $dayKwhOptions = $options['dayKwh'];

        // Check if response contains wanted values
        if (!isset($info[$currentWattOptions['key']])) {
            throw new Exception(__METHOD__ . '; Current watt not found in info response');
        }

        if (!isset($info[$totalKwhOptions['key']])) {
            throw new Exception(__METHOD__ . '; Total Kwh not found in info response');
        }

        if (!isset($info[$dayKwhOptions['key']])) {
            throw new Exception(__METHOD__ . '; Day Kwh not found in info response');
        }

        // Retrieve wanted values
        $currentWatt = $info[$currentWattOptions['key']] / $currentWattOptions['devide'];
        $totalKwh = $info[$totalKwhOptions['key']] / $totalKwhOptions['devide'];
        $dayKwh = $info[$dayKwhOptions['key']] / $dayKwhOptions['devide'];

        return new Status((float)$totalKwh, (float)$dayKwh, (int)$currentWatt);
    }

    /**
     * Get search options for different inverter version
     * @return array
     */
    private function getSearchOptions(): array
    {
        return [
            'myDeviceArray[0]="' => [
                'currentW' => ['key' => 5, 'devide' => 1],
                'totalkWh' => ['key' => 7, 'devide' => 10],
                'dayKwh' => ['key' => 6, 'devide' => 100]
            ]
        ];
    }
}
