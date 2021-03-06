<?php

namespace SonosCron\Services\Handler;

use SonosCron\Services\SonosApiClient;

interface HandlerInterface
{
    /**
     * @param SonosApiClient $sonosApiClient
     */
    public function __construct(SonosApiClient $sonosApiClient);

    /**
     * @return mixed
     */
    public function handle(array $payload);

    /**
     * @return string
     */
    public function getName();
}
