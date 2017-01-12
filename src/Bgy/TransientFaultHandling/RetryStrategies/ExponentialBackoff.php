<?php

namespace Bgy\TransientFaultHandling\RetryStrategies;

use Bgy\TransientFaultHandling\RetryStrategy;
use Bgy\TransientFaultHandling\ShouldRetry;
use Throwable;

/**
 * @author Boris Guéry <guery.b@gmail.com>
 */
class ExponentialBackoff implements RetryStrategy
{
    private $retryCount;
    private $minBackoff;
    private $maxBackoff;
    private $deltaBackoff;
    private $firstFastRetry;

    public function __construct(int $retryCount, float $minBackoff, float $maxBackoff,
        float $deltaBackoff, bool $firstFastRetry = true)
    {
        $this->retryCount    = $retryCount;
        $this->minBackoff    = $minBackoff;
        $this->maxBackoff    = $maxBackoff;
        $this->deltaBackoff  = $deltaBackoff;
        $this->firstFastRetry = $firstFastRetry;
    }

    public function getShouldRetry(): ShouldRetry
    {
        /** @var ShouldRetry $shouldRetry */
        $shouldRetry = new class($this->retryCount, $this->minBackoff, $this->maxBackoff, $this->deltaBackoff) implements ShouldRetry {
            private $retryCount;
            private $minBackoff;
            private $maxBackoff;
            private $deltaBackoff;

            public function __construct($retryCount, $minBackoff, $maxBackoff, $deltaBackoff)
            {
                $this->retryCount   = $retryCount;
                $this->minBackoff   = $minBackoff;
                $this->maxBackoff   = $maxBackoff;
                $this->deltaBackoff = $deltaBackoff;
            }


            public function __invoke(int $currentRetryCount, Throwable $lastException, int &$interval)
            {
                if ($currentRetryCount < $this->retryCount) {
                    $delta = (int) ((
                        pow(2.0, $currentRetryCount) - 1.0)
                      * random_int((int)($this->deltaBackoff * 0.8), (int)($this->deltaBackoff * 1.2))
                    );
                    $interval = (int) min($this->minBackoff + $delta, $this->maxBackoff);

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
