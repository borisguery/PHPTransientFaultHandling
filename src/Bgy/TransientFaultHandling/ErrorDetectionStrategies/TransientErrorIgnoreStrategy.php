<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Bgy\TransientFaultHandling\ErrorDetectionStrategies;

use Bgy\TransientFaultHandling\ErrorDetectionStrategy;
use Throwable;

class TransientErrorIgnoreStrategy implements ErrorDetectionStrategy
{
    public function isTransient(Throwable $e): bool
    {
        return false;
    }
}
