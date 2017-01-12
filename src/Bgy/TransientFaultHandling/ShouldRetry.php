<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Bgy\TransientFaultHandling;

use Throwable;

interface ShouldRetry
{
    public function __invoke(int $currentRetryCount, Throwable $lastError, int &$interval);
}

