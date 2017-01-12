<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Bgy\TransientFaultHandling;

interface RetryStrategy
{
    public function getShouldRetry(): ShouldRetry;

    public function hasFirstFastRetry(): bool;
}
