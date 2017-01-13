<?php

namespace Bgy\Tests\TransientFaultHandling;

use Bgy\TransientFaultHandling\ErrorDetectionStrategy;
use Bgy\TransientFaultHandling\RetryPolicy;
use Bgy\TransientFaultHandling\RetryStrategy;
use Bgy\TransientFaultHandling\ShouldRetry;
use PHPUnit\Framework\TestCase;

/**
 * @author Boris Guéry <guery.b@gmail.com>
 */
class RetryPolicyTest extends TestCase
{
    public function testSimpleRetryPolicy()
    {
        $maxRetryCount = 3;
        $actionException = $this->getMockBuilder(\Exception::class)
            ->setMockClassName('ActionException')
            ->getMock()
        ;

        $errorDetectionStrategy = $this->createMock(ErrorDetectionStrategy::class);
        $errorDetectionStrategy
            ->expects($this->exactly(3))
            ->method('isTransient')
                ->willReturn(true)
        ;

        $shouldRetry = $this->createMock(ShouldRetry::class);
        $shouldRetry
            ->expects($this->exactly(3))
            ->method('__invoke')
            ->withConsecutive(
                [
                    $this->equalTo(1),
                    $this->isInstanceOf(\Throwable::class),
                    $this->anything(),
                ]
                    ,
                [
                    $this->equalTo(2),
                    $this->isInstanceOf(\Throwable::class),
                    $this->anything(),
                ],
                [
                    $this->equalTo(3),
                    $this->isInstanceOf(\Throwable::class),
                    $this->anything(),
                ]
            )
            ->willReturnCallback(function ($retryCount, $lastError, &$delay) use ($maxRetryCount) {
                $delay += 2;
                if ($retryCount < $maxRetryCount) {

                    return true;
                }

                return false;
            });
        ;

        $retryStrategy = $this->createMock(RetryStrategy::class);
        $retryStrategy
            ->expects($this->exactly(1))
            ->method('hasFirstFastRetry')
                ->willReturn(false)
        ;
        $retryStrategy
            ->expects($this->exactly(1))
            ->method('getShouldRetry')
                ->willReturn($shouldRetry)
        ;

        $retryPolicy = new RetryPolicy($errorDetectionStrategy, $retryStrategy);

        $this->expectException('ActionException');

        $retryPolicy->execute(function () use ($actionException) {

            throw $actionException;
        });
    }
}
