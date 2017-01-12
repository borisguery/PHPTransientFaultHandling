<?php

namespace Bgy\TransientFaultHandling\RetryStrategies;

use Bgy\TransientFaultHandling\RetryStrategy;
use Bgy\TransientFaultHandling\ShouldRetry;
use Throwable;

/**
 * @author Boris Guéry <guery.b@gmail.com>
 */
class FixedInterval implements RetryStrategy
{
    private $retryCount;
    private $retryIntervalInMicroseconds;
    private $firstFastRetry;

    public function __construct(int $retryCount, int $retryIntervalInMicroseconds, bool $firstFastRetry = true)
    {
        $this->retryCount                  = $retryCount;
        $this->retryIntervalInMicroseconds = $retryIntervalInMicroseconds;
        $this->firstFastRetry               = $firstFastRetry;
    }

    public function getShouldRetry(): ShouldRetry
    {
        /** @var ShouldRetry $shouldRetry */
        $shouldRetry = new class($this->retryCount, $this->retryIntervalInMicroseconds) implements ShouldRetry {
            private $retryCount;
            private $retryIntervalInMicroseconds;

            public function __construct($retryCount, $retryIntervalInMicroseconds)
            {
                $this->retryCount                  = $retryCount;
                $this->retryIntervalInMicroseconds = $retryIntervalInMicroseconds;
            }

            public function __invoke(int $currentRetryCount, Throwable $lastException, int &$interval)
            {
                if ($currentRetryCount < $this->retryCount) {
                    $interval = $this->retryIntervalInMicroseconds;

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
