<?php

namespace Osl\SonosCron\Tests\Unit;

use GuzzleHttp\Client;
use Psr\Http\Message\RequestInterface;
use Osl\SonosCron\Services\SonosApiClient;

/**
 * Unit tests for SonosApiClient
 *
 * @author Chris Paterson <chris.paterson@student.com>
 */
class SonosApiClientTest extends AbstractMockeryTest
{
    /**
     * Assert that a call to sayToRoom() results in the correct HTTP call to the API
     */
    public function testSayToRoomSuccess()
    {
        $params = array(
            'room' => '3F Engineering',
            'message' => 'This is a cool message',
            'language' => 'en-gb',
        );

        $apiBaseUrl = 'http://localhost:5000';
        $expectedUrl = sprintf(
            '%s/%s/say/%s/%s',
            $apiBaseUrl,
            $params['room'],
            $params['message'],
            $params['language']
        );

        $mockRequest = \Mockery::mock(RequestInterface::class);

        $mockHttpClient = \Mockery::mock(Client::class);
        $mockHttpClient
            ->shouldReceive('get')
            ->once()
            ->with($expectedUrl)
            ->andReturn($mockRequest);
        $mockHttpClient
            ->shouldReceive('send')
            ->once()
            ->with($mockRequest);

        $sonosApiClient = new SonosApiClient($mockHttpClient, $apiBaseUrl);
        $sonosApiClient->sayToRoom($params['room'], $params['message'], $params['language']);
    }
}
