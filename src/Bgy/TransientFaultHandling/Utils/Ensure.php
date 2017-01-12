<?php

namespace Bgy\TransientFaultHandling\Utils;

use Assert\Assertion;

/**
 * @author Boris Guéry <guery.b@gmail.com>
 */
class Ensure extends Assertion
{
    protected static $exceptionClass = EnsureFailed::class;
}
