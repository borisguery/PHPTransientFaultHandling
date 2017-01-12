<?php

namespace Bgy\TransientFaultHandling\RetryStrategies;

use Bgy\TransientFaultHandling\RetryStrategy;
use Bgy\TransientFaultHandling\ShouldRetry;
use Throwable;

/**
 * @author Boris Guéry <guery.b@gmail.com>
 */
class Incremental implements RetryStrategy
{
    private $retryCount;
    private $initialIntervalInMicroseconds;
    private $incrementInMicroseconds;
    private $firstFastRetry;

    public function __construct(int $retryCount, int $initialIntervalInMicroseconds, int $incrementInMicroseconds, bool $firstFastRetry = true)
    {
        $this->retryCount                    = $retryCount;
        $this->initialIntervalInMicroseconds = $initialIntervalInMicroseconds;
        $this->incrementInMicroseconds       = $incrementInMicroseconds;
        $this->firstFastRetry                 = $firstFastRetry;
    }

    public function getShouldRetry(): ShouldRetry
    {
        /** @var ShouldRetry $shouldRetry */
        $shouldRetry = new class($this->retryCount, $this->initialIntervalInMicroseconds, $this->incrementInMicroseconds) implements ShouldRetry {
            private $retryCount;
            private $initialIntervalInMicroseconds;
            private $incrementInMicroseconds;

            public function __construct($retryCount, $initialIntervalInMicroseconds, $incrementInMicroseconds)
            {
                $this->retryCount                    = $retryCount;
                $this->initialIntervalInMicroseconds = $initialIntervalInMicroseconds;
                $this->incrementInMicroseconds       = $incrementInMicroseconds;
            }

            public function __invoke(int $currentRetryCount, Throwable $lastException, int &$interval)
            {
                if ($currentRetryCount < $this->retryCount) {
                    $interval = $this->initialIntervalInMicroseconds + ($this->incrementInMicroseconds * $currentRetryCount);

                    return true;
                }

                $interval = 0;

                return false;
            }
        };

        return $shouldRetry;
    }

    public function hasFirstFastRetry(): bool
    {
        return $this->firstFastRetry;
    }
}
