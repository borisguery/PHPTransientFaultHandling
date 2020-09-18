<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Bgy\TransientFaultHandling\ErrorDetectionStrategies;

use Bgy\TransientFaultHandling\ErrorDetectionStrategy;
use Throwable;

class IncludeStrategy implements ErrorDetectionStrategy
{
    private array $includedExceptions;

    public function __construct(array $includedExceptions)
    {
        $this->includedExceptions = $includedExceptions;
    }

    public function isTransient(Throwable $e): bool
    {
        foreach ($this->includedExceptions as $includedException) {
            if ($e instanceof $includedException) {

                return true;
            }
        }

        return false;
    }
}
