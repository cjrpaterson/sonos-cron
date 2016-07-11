<?php

namespace SonosCron\Services;

use GuzzleHttp\Client;

/**
 * Class SonosApiClient
 *
 * @author Chris Paterson
 */
class SonosApiClient
{
    const ENDPOINT_SAY_TO_ROOM = '/%s/say/%s/%s';

    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var string
     */
    private $apiBaseUrl;

    /**
     * @param Client $httpClient
     * @param string $apiBaseUrl
     */
    public function __construct(Client $httpClient, $apiBaseUrl)
    {
        $this->httpClient = $httpClient;
        $this->apiBaseUrl = $apiBaseUrl;
    }

    /**
     * Send say command for specific room
     *
     * @param string $roomName
     * @param string $message
     * @param string $language
     */
    public function sayToRoom($roomName, $message, $language)
    {
        $url = $this->apiBaseUrl . sprintf(self::ENDPOINT_SAY_TO_ROOM, $roomName, $message, $language);
        $request = $this->httpClient->get($url);

        $this->httpClient->send($request);

        echo $url;
    }
}
