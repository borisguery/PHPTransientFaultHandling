<?php

namespace Bgy\Tests\TransientFaultHandling;

use Bgy\TransientFaultHandling\ErrorDetectionStrategy;
use Bgy\TransientFaultHandling\EventDispatcher;
use Bgy\TransientFaultHandling\Events;
use Bgy\TransientFaultHandling\Retrying;
use Bgy\TransientFaultHandling\RetryPolicy;
use Bgy\TransientFaultHandling\RetryStrategy;
use Bgy\TransientFaultHandling\ShouldRetry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use PHPUnit_Framework_MockObject_Matcher_ConsecutiveParameters;

/**
 * @author Boris Guéry <guery.b@gmail.com>
 */
class RetryPolicyTest extends TestCase
{
    private $maxRetryCount;
    private $actionException;
    private $errorDetectionStrategy;
    private $retryStrategy;
    private $defaultDelay;

    protected function setUp(): void
    {
        $this->maxRetryCount = $maxRetryCount = 4;
        $this->defaultDelay  = $defaultDelay  = 2;

        $actionException = $this->getMockBuilder(\Exception::class)
            ->setMockClassName('ActionException')
            ->getMock()
        ;

        $this->actionException = $actionException;

        $errorDetectionStrategy = $this->createMock(ErrorDetectionStrategy::class);
        $errorDetectionStrategy
            ->expects($this->exactly($this->maxRetryCount))
            ->method('isTransient')
            ->willReturn(true)
        ;

        $this->errorDetectionStrategy = $errorDetectionStrategy;

        $shouldRetry = $this->createMock(ShouldRetry::class);
        $mockedMethod = $shouldRetry
            ->expects($this->exactly($this->maxRetryCount))
            ->method('__invoke')
                ->willReturnCallback(function ($retryCount, $lastError, &$delay) use ($maxRetryCount, $defaultDelay) {
                    $delay += $defaultDelay;
                    if ($retryCount < $maxRetryCount) {

                        return true;
                    }

                    return false;
                })
        ;
        // Because of the variadic arguments used by withConsecutive I can't
        // easily generate the assertion based on the number of retry count.
        call_user_func_array([$mockedMethod, 'withConsecutive'], array_map(function ($i) use ($maxRetryCount) {
            return [
                $this->equalTo($i),
                $this->isInstanceOf(\Throwable::class),
                $this->anything(),
            ];
        }, range(1, $maxRetryCount)));

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

        $this->retryStrategy = $retryStrategy;
    }

    public function testSimpleRetryPolicy()
    {
        $retryPolicy = new RetryPolicy($this->errorDetectionStrategy, $this->retryStrategy);

        $this->expectException('ActionException');

        $actionException = $this->actionException;

        $retryPolicy->execute(function () use ($actionException) {

            throw $actionException;
        });
    }
}
