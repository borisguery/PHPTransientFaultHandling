<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Bgy\TransientFaultHandling\ErrorDetectionStrategies;

use Bgy\TransientFaultHandling\ErrorDetectionStrategy;
use Throwable;

class ExcludeStrategy implements ErrorDetectionStrategy
{
    private array $excludedExceptions;

    public function __construct(array $excludedExceptions)
    {
        $this->excludedExceptions = $excludedExceptions;
    }

    public function isTransient(Throwable $e): bool
    {
        foreach ($this->excludedExceptions as $excludedException) {
            if ($e instanceof $excludedException) {

                return false;
            }
        }

        return true;
    }
}
