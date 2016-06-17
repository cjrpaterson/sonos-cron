<?php

namespace Osl\SonosCron\Tests\Unit;

/**
 * Extended by unit tests using Mockery
 *
 * @author Chris Paterson <chris.paterson@student.com>
 */
abstract class AbstractMockeryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * {@inheritdoc}
     */
    public function tearDown() {
        \Mockery::close();
    }
}