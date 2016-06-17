<?php

namespace Osl\SonosCron\Services\Handler;

use Osl\SonosCron\Services\SonosApiClient;

/**
 * Handles calls to the say Sonos endpoint
 *
 * @author Chris Paterson <chris.paterson@student.com>
 */
class SayHandler implements HandlerInterface
{
    const HANDLER_NAME = 'say';

    const DEFAULT_LANGUAGE = 'en-gb';

    /**
     * @var SonosApiClient
     */
    private $sonosApiClient;

    /**
     * {@inheritdoc}
     */
    public function __construct(SonosApiClient $sonosApiClient)
    {
        $this->sonosApiClient = $sonosApiClient;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(array $payload)
    {
        $room = $payload['room'];
        $message = $payload['message'];
        $language = isset($payload['language']) ? $payload['language'] : self::DEFAULT_LANGUAGE;

        $this->sonosApiClient->sayToRoom($room, $message, $language);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::HANDLER_NAME;
    }
}