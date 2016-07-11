<?php

namespace SonosCron\Tests\Unit;

use Aws\Sqs\SqsClient;
use Mockery\MockInterface;
use SonosCron\Services\QueueProcessor;
use SonosCron\Services\Handler\HandlerInterface;

/**
 * Unit tests for QueueProcessor
 *
 * @author Chris Paterson
 */
class QueueProcessorTest extends AbstractMockeryTest
{
    /**
     * @var MockInterface|SqsClient
     */
    private $mockSqsClient;

    /**
     * @var string
     */
    private $sqsQueueUrl = 'http://some_queue_url';

    /**
     * @var array
     */
    private $testMessages = array();

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->mockSqsClient = \Mockery::mock(SqsClient::class);
        $this->testMessages = array(
            array(
                'ReceiptHandle' => 'abc1234-receipt',
                'Body' => json_encode(array(
                    'handler' => 'test',
                    'payload' => array(
                        array(
                            'message' => 'Test message blah blah',
                            'language' => 'en-gb',
                        )
                    )
                ))
            ),
            array(
                'ReceiptHandle' => 'def5678-receipt',
                'Body' => json_encode(array(
                    'handler' => 'test',
                    'payload' => array(
                        'message' => 'Test message blah blah',
                        'language' => 'en-gb',
                    )
                ))
            )
        );
    }

    /**
     * Assert that nothing happens if the queue is empty
     */
    public function testSuccessNoWaitingMessages()
    {
        $this->addSqsClientReceiveMessageAssertion(array());
        $this->mockSqsClient->shouldNotReceive('deleteMessage');

        $this->createQueueProcessor()->process();
    }

    /**
     * Assert that multiple messages with a known handler are processed successfully
     */
    public function testSuccessWithWaitingMessagesMatchingKnownHandler()
    {
        $this->addSqsClientReceiveMessageAssertion(array('Messages' => $this->testMessages));

        $mockHandler = $this->createMockHandler('test');
        $this->addHandlerAssertions($mockHandler);

        $this->addDeleteMessageAssertions();

        $queueProcessor = $this->createQueueProcessor();
        $queueProcessor->registerHandler($mockHandler);
        $queueProcessor->process();
    }

    /**
     * Assert that multiple messages with a matching and missing handler are processed successfully
     */
    public function testSuccessWithMatchingAndMissingHandler()
    {
        $mockHandler = $this->createMockHandler('test');
        $this->addHandlerAssertions($mockHandler);

        // Add a cheeky message which doesn't have a handler registered
        $this->testMessages[] = array(
            'ReceiptHandle' => '1234-new-receipt',
            'Body' => json_encode(array(
                'handler' => 'random-handler',
                'payload' => array(
                    'message' => 'Test message blah blah',
                    'language' => 'en-gb',
                )
            ))
        );

        $this->addDeleteMessageAssertions();

        $this->addSqsClientReceiveMessageAssertion(array('Messages' => $this->testMessages));

        $queueProcessor = $this->createQueueProcessor();
        $queueProcessor->registerHandler($mockHandler);
        $queueProcessor->process();
    }
    
    /**
     * Create and return mock instance of HandlerInterface which returns $handlerName when getName() is called
     * @param string $handlerName
     *
     * @return MockInterface|HandlerInterface
     */
    private function createMockHandler($handlerName)
    {
        $mockHandler = \Mockery::mock(HandlerInterface::class);
        $mockHandler
            ->shouldReceive('getName')
            ->once()
            ->withNoArgs()
            ->andReturn($handlerName);

        return $mockHandler;
    }

    /**
     * Create and return instance of QueueProcessor
     *
     * @return QueueProcessor
     */
    private function createQueueProcessor()
    {
        return new QueueProcessor($this->mockSqsClient, $this->sqsQueueUrl);
    }

    /**
     * Add assertion that $this->mockSqsClient->receiveMessage() is called, and returns $response
     *
     * @param array $response
     */
    private function addSqsClientReceiveMessageAssertion(array $response)
    {
        $this->mockSqsClient
            ->shouldReceive('receiveMessage')
            ->once()
            ->with(array(
                'QueueUrl' => $this->sqsQueueUrl,
                'WaitTimeSeconds' => QueueProcessor::QUEUE_POLL_WAIT_TIME,
            ))
            ->andReturn($response);
    }

    /**
     * Add assertion that for each test message, $sqsClient->deleteMessage() is called
     */
    private function addDeleteMessageAssertions()
    {
        foreach ($this->testMessages as $msg) {
            $this->mockSqsClient
                ->shouldReceive('deleteMessage')
                ->once()
                ->with(array(
                    'QueueUrl' => $this->sqsQueueUrl,
                    'ReceiptHandle' => $msg['ReceiptHandle'],
                ));
        }
    }

    /**
     * Add assertion that for each test message, $handler->handle() is called
     *
     * @param HandlerInterface $handler
     */
    private function addHandlerAssertions(HandlerInterface $handler)
    {
        foreach ($this->testMessages as $msg) {
            $handler
                ->shouldReceive('handle')
                ->once()
                ->with(json_decode($msg['Body'], true)['payload']);
        }
    }
}
