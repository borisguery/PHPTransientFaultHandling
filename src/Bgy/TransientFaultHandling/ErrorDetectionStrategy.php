<?php

namespace Bgy\TransientFaultHandling;

use Throwable;

interface ErrorDetectionStrategy
{
    /**
     * Determines whether the specified exception represents a transient failure
     * that can be compensated by a retry.
     */
    public function isTransient(Throwable $e): bool;
}
