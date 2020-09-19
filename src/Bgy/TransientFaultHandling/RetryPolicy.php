<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Bgy\TransientFaultHandling;

use Throwable;

class RetryPolicy
{
    private ErrorDetectionStrategy $errorDetectionStrategy;
    private RetryStrategy $retryStrategy;
    private bool $wrapLastError;

    public function __construct(ErrorDetectionStrategy $errorDetectionStrategy, RetryStrategy $retryStrategy,
                                bool $wrapLastError = false)
    {
        $this->errorDetectionStrategy = $errorDetectionStrategy;
        $this->retryStrategy = $retryStrategy;
        $this->wrapLastError = $wrapLastError;
    }

    public function execute(callable $action)
    {
        $retryCount = 0;
        $delay = 0;
        $lastException = null;

        $shouldRetry = $this->retryStrategy->getShouldRetry();

        for (;;) {
            try {

                return $action();
            } catch (Throwable $exception) {
                $lastException = $exception;

                if (!$this->errorDetectionStrategy->isTransient($exception)) {

                    throw $exception;
                }

                if (!$shouldRetry(++$retryCount, $lastException, $delay)) {

                    if ($this->wrapLastError) {

                        throw new RetryLimitExceeded($exception, $retryCount);
                    }

                    throw $exception;
                }
            }

            if ($delay <= 0) {
                $delay = 0;
            }

            if ($retryCount > 1 || !$this->retryStrategy->hasFirstFastRetry()) {
                usleep($delay);
            }
        }
    }
}
