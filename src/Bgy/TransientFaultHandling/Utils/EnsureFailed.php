<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Bgy\TransientFaultHandling\Utils;

use Assert\InvalidArgumentException;
use Bgy\TransientFaultHandling\Exception;

class EnsureFailed extends InvalidArgumentException implements Exception
{}
