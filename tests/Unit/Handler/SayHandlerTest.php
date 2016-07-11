<?php

namespace SonosCron\Tests\Unit\Handler;

use Mockery\MockInterface;
use SonosCron\Services\Handler\SayHandler;
use SonosCron\Services\SonosApiClient;
use SonosCron\Tests\Unit\AbstractMockeryTest;

/**
 * Unit tests for SayHandler
 *
 * @author Chris Paterson
 */
class SayHandlerTest extends AbstractMockeryTest
{
    /**
     * Assert that when passed a payload, the Handler correctly calls the SonosApiClient
     */
    public function testSayToRoomSuccessWithLanguage()
    {
        $payload = array(
            'room' => '3F Engineering',
            'message' => 'Hey this is a cool message',
            'language' => 'en-gb',
        );

        $mockSonosApiClient = $this->createMockSonosApiClient(
            $payload['room'],
            $payload['message'],
            $payload['language']
        );

        $sayHandler = new SayHandler($mockSonosApiClient);
        $sayHandler->handle($payload);
    }

    /**
     * Assert that when passed a payload which has no language, the Handler uses a default language and correctly calls
     * the SonosApiClient
     */
    public function testSayToRoomSuccessWithoutLanguage()
    {
        $payload = array(
            'room' => '3F Engineering',
            'message' => 'Hey this is a cool message',
        );

        $mockSonosApiClient = $this->createMockSonosApiClient(
            $payload['room'],
            $payload['message'],
            SayHandler::DEFAULT_LANGUAGE
        );

        $sayHandler = new SayHandler($mockSonosApiClient);
        $sayHandler->handle($payload);
    }

    /**
     * Create mock SonosApiClient with expected call to sayToRoom($expectedRoom, $expectedMessage, $expectedLanguage)
     *
     * @param string $expectedRoom
     * @param string $expectedMessage
     * @param string $expectedLanguage
     *
     * @return MockInterface|SonosApiClient
     */
    private function createMockSonosApiClient($expectedRoom, $expectedMessage, $expectedLanguage)
    {
        $mockSonosApiClient = \Mockery::mock(SonosApiClient::class);
        $mockSonosApiClient
            ->shouldReceive('sayToRoom')
            ->once()
            ->with($expectedRoom, $expectedMessage, $expectedLanguage);

        return $mockSonosApiClient;
    }
}