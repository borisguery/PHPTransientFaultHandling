<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Bgy\TransientFaultHandling\ErrorDetectionStrategies;

use Bgy\TransientFaultHandling\ErrorDetectionStrategy;
use Throwable;

class TransientErrorCatchAllStrategy implements ErrorDetectionStrategy
{
    public function isTransient(Throwable $e): bool
    {
        return true;
    }
}
