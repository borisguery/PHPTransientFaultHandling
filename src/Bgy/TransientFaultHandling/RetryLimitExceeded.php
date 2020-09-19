<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Bgy\TransientFaultHandling;

use Throwable;

class RetryLimitExceeded extends \Exception
{
    private int $retryCount;

    public function __construct(Throwable $wrappedLastError, int $retryCount)
    {
        parent::__construct(
            sprintf('Retry policy exceeded the number of allowed retries (max: %d)', $retryCount),
            0,
            $wrappedLastError
        );
        $this->retryCount = $retryCount;
    }

    public function getRetryCount(): int
    {
        return $this->retryCount;
    }
}
