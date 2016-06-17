<?php

namespace Osl\SonosCron\Tests\Unit;

use Aws\Sqs\SqsClient;
use Osl\SonosCron\Services\SqsClientFactory;

/**
 * Unit tests for SqsClientFactory
 *
 * @author Chris Paterson <chris.paterson@student.com>
 */
class SqsClientFactoryTest extends AbstractMockeryTest
{
    /**
     * Assert factory returns correctly-initialised instance of SqsClient
     */
    public function testSuccess()
    {
        $params = array(
            'accessKey' => 'This_is_my_access_key',
            'accessSecret' => 'shhhhtellnobody',
            'region' => 'eu-west-1234',
        );
        $sqsClientFactory = new SqsClientFactory($params['region'], $params['accessKey'], $params['accessSecret']);
        $sqsClient = $sqsClientFactory->createSqsClient();

        $this->assertInstanceOf(SqsClient::class, $sqsClient);
        $this->assertEquals($params['accessKey'], $sqsClient->getCredentials()->getAccessKeyId());
        $this->assertEquals($params['accessSecret'], $sqsClient->getCredentials()->getSecretKey());
        $this->assertEquals($params['region'], $sqsClient->getConfig('region'));
    }
}