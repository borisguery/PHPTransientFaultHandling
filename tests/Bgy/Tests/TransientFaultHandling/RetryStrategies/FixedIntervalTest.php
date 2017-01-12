<?php

namespace Bgy\Tests\TransientFaultHandling\RetryStrategies;

use Bgy\TransientFaultHandling\RetryStrategies\FixedInterval;
use Bgy\TransientFaultHandling\ShouldRetry;
use PHPUnit\Framework\TestCase;

/**
 * @author Boris Guéry <guery.b@gmail.com>
 */
class FixedIntervalTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testFixedInterval(int $retryCount, int $retryIntervalInMicroseconds)
    {
        $retryStrategy = new FixedInterval($retryCount, $retryIntervalInMicroseconds);
        $shouldRetry = $retryStrategy->getShouldRetry();

        $this->assertInstanceOf(ShouldRetry::class, $shouldRetry);

        $delay = -1;

        for ($i = 0; $i < $retryCount; $i++) {
            $this->assertTrue($shouldRetry($i, new \Exception(), $delay));
            $this->assertEquals($retryIntervalInMicroseconds, $delay);
        }

        $this->assertFalse($shouldRetry($i+1, new \Exception(), $delay));
    }

    public function dataProvider()
    {
        return [
            '1 retry after 1 second'     => ['retryCount' => 1, 1 * ONE_SECOND_IN_MICROSECONDS],
            '1 retry after 2 seconds'    => ['retryCount' => 1, 2 * ONE_SECOND_IN_MICROSECONDS],
            '3 retries after 1 second'   => ['retryCount' => 1, 1 * ONE_SECOND_IN_MICROSECONDS],
            '3 retries after 10 seconds' => ['retryCount' => 1, 1 * ONE_SECOND_IN_MICROSECONDS],
        ];
    }
}
