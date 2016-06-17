<?php

namespace Osl\SonosCron\Services;

use Aws\Sqs\SqsClient;

/**
 * Constructs instances of SqsClient
 *
 * @author Chris Paterson <chris.paterson@student.com>
 */
class SqsClientFactory
{
    /**
     * @var string
     */
    private $accessKey;

    /**
     * @var string
     */
    private $accessSecret;

    /**
     * @var string
     */
    private $region;

    /**
     * @param string $accessKey
     * @param string $accessSecret
     * @param string $region
     */
    public function __construct($region, $accessKey, $accessSecret)
    {
        $this->region = $region;
        $this->accessKey = $accessKey;
        $this->accessSecret = $accessSecret;
    }

    /**
     * @return SqsClient
     */
    public function createSqsClient()
    {
        return SqsClient::factory(array(
            'region' => $this->region,
            'key' => $this->accessKey,
            'secret' => $this->accessSecret,
        ));
    }
}