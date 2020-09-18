<?php

namespace Bgy\Tests\TransientFaultHandling\RetryStrategies;

use Bgy\TransientFaultHandling\RetryStrategies\Incremental;
use Bgy\TransientFaultHandling\ShouldRetry;
use PHPUnit\Framework\TestCase;

/**
 * @author Boris Guéry <guery.b@gmail.com>
 */
class IncrementalTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testIncremental(int $retryCount, float $initialIntervalInMicroseconds, int $incrementInMicroseconds)
    {
        $retryStrategy = new Incremental($retryCount, $initialIntervalInMicroseconds, $incrementInMicroseconds);
        $shouldRetry = $retryStrategy->getShouldRetry();

        $this->assertInstanceOf(ShouldRetry::class, $shouldRetry);

        $delay = -1;

        for ($i = 0; $i < $retryCount; $i++) {
            $this->assertTrue($shouldRetry($i, new \Exception(), $delay));
            if (0 === $i) {
                $this->assertEquals($initialIntervalInMicroseconds, $delay);
                continue;
            }

            $this->assertEquals($initialIntervalInMicroseconds + ($i * $incrementInMicroseconds), $delay);
        }

        $this->assertFalse($shouldRetry($i+1, new \Exception(), $delay));
    }

    public function dataProvider()
    {
        return [
            '1 retry, first after 1 second, and then increase by 2 seconds'    => [
                'retryCount'                    => 1,
                'initialIntervalInMicroseconds' => 1 * ONE_SECOND_IN_MICROSECONDS,
                'incrementInMicroseconds'       => 2 * ONE_SECOND_IN_MICROSECONDS
            ],
            '3 retries, first after 2 seconds, and then increase by 3 seconds' => [
                'retryCount'                    => 3,
                'initialIntervalInMicroseconds' => 2 * ONE_SECOND_IN_MICROSECONDS,
                'incrementInMicroseconds'       => 3 * ONE_SECOND_IN_MICROSECONDS
            ],
            '10 retries, first after 0.5 seconds, and then increase by 10 seconds' => [
                'retryCount'                    => 10,
                'initialIntervalInMicroseconds' => 0.5 * ONE_SECOND_IN_MICROSECONDS,
                'incrementInMicroseconds'       => 10 * ONE_SECOND_IN_MICROSECONDS
            ],
        ];
    }
}
