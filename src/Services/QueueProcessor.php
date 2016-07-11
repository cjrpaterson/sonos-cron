<?php

namespace SonosCron\Services;

use Aws\Sqs\SqsClient;
use SonosCron\Services\Handler\HandlerInterface;

/**
 * Processes messages from queue, calling handle() in relevant handler
 *
 * @author Chris Paterson
 */
class QueueProcessor
{
    const QUEUE_POLL_WAIT_TIME = 20;

    /**
     * @var SqsClient
     */
    private $sqsClient;

    /**
     * @var string
     */
    private $sqsQueueUrl;

    /**
     * @var array
     */
    private $handlers = array();

    /**
     * @param SqsClient $sqsClient
     * @param string    $sqsQueueUrl
     */
    public function __construct(SqsClient $sqsClient, $sqsQueueUrl)
    {
        $this->sqsClient = $sqsClient;
        $this->sqsQueueUrl = $sqsQueueUrl;
    }

    /**
     * Retrieve messages from the queue and pass to the relevant handler
     */
    public function process()
    {
        $response = $this->sqsClient->receiveMessage(array(
            'QueueUrl' => $this->sqsQueueUrl,
            'WaitTimeSeconds' => self::QUEUE_POLL_WAIT_TIME,
        ));
        
        if (isset($response['Messages'])) {
            foreach ($response['Messages'] as $message) {
                $body = json_decode($message['Body'], true);
                $handler = $this->getRegisteredHandlerByName($body['handler']);

                if ($handler) {
                    $handler->handle($body['payload']);
                } else {
                    // @TODO Log and move on
                    echo 'No handler' . PHP_EOL;
                }

                $this->sqsClient->deleteMessage(array(
                    'QueueUrl' => $this->sqsQueueUrl,
                    'ReceiptHandle' => $message['ReceiptHandle'],
                ));
            }
        } else {
            echo 'No messages.' . PHP_EOL;
        }
    }

    /**
     * Register a handler, which should be called when messages with the handler name are retrieved from the queue
     *
     * @param HandlerInterface $handler
     *
     * @return $this
     */
    public function registerHandler(HandlerInterface $handler)
    {
        $this->handlers[$handler->getName()] = $handler;

        return $this;
    }

    /**
     * Retrieve registered handler by name
     *
     * @param $handlerName
     *
     * @return bool|HandlerInterface
     */
    private function getRegisteredHandlerByName($handlerName)
    {
        return isset($this->handlers[$handlerName]) ? $this->handlers[$handlerName] : false;
    }
}
