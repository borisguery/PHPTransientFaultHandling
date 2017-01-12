<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Bgy\TransientFaultHandling;

use Throwable;

class RetryPolicy
{
    /**
     * @var ErrorDetectionStrategy
     */
    private $errorDetectionStrategy;

    /**
     * @var RetryStrategy
     */
    private $retryStrategy;

    public function __construct(ErrorDetectionStrategy $errorDetectionStrategy, RetryStrategy $retryStrategy)
    {
        $this->errorDetectionStrategy = $errorDetectionStrategy;
        $this->retryStrategy          = $retryStrategy;
    }

    public function execute(callable $action)
    {
        $retryCount    = 0;
        $delay         = 0;
        $lastException = null;

        $shouldRetry = $this->retryStrategy->getShouldRetry();

        for (;;) {
            try {

                return call_user_func($action);
            } catch (Throwable $exception) {
                $lastException = $exception;

                if (!($this->errorDetectionStrategy->isTransient($exception)
                    && $shouldRetry(++$retryCount, $lastException, $delay))) {

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
