<?php

namespace Bgy\Tests\TransientFaultHandling\RetryStrategies;

use Bgy\TransientFaultHandling\RetryStrategies\ExponentialBackoff;
use Bgy\TransientFaultHandling\ShouldRetry;
use PHPUnit\Framework\TestCase;

/**
 * @author Boris Guéry <guery.b@gmail.com>
 */
class ExponentialBackoffTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testFixedInterval(int $retryCount, float $minBackoff, float $maxBackoff,
        float $deltaBackoff)
    {
        $retryStrategy = new ExponentialBackoff($retryCount, $minBackoff, $maxBackoff, $deltaBackoff);
        $shouldRetry = $retryStrategy->getShouldRetry();

        $this->assertInstanceOf(ShouldRetry::class, $shouldRetry);

        $delay = -1;

        for ($i = 0; $i < $retryCount; $i++) {
            $this->assertTrue($shouldRetry($i, new \Exception(), $delay));
            $this->assertGreaterThanOrEqual($minBackoff, $delay);
            $this->assertLessThanOrEqual($maxBackoff, $delay);
        }
    }

    public function dataProvider()
    {
        return [
            '1 retry, first after 1 second' => [
                'retryCount'   => 1,
                'minBackoff'   => 1  * ONE_SECOND_IN_MICROSECONDS,
                'maxBackoff'   => 60 * ONE_SECOND_IN_MICROSECONDS,
                'deltaBackoff' => 1.5 * ONE_SECOND_IN_MICROSECONDS,
            ],
            '10 retries, first after 1 second, and then factor by a delta of 2 seconds but for no more than 60 seconds' => [
                'retryCount'   => 10,
                'minBackoff'   => 1  * ONE_SECOND_IN_MICROSECONDS,
                'maxBackoff'   => 60 * ONE_SECOND_IN_MICROSECONDS,
                'deltaBackoff' => 2 * ONE_SECOND_IN_MICROSECONDS,
            ],
        ];
    }
}
