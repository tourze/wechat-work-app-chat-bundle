<?php

namespace WechatWorkAppChatBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use WechatWorkAppChatBundle\Exception\MessageSendException;

/**
 * @internal
 */
#[CoversClass(MessageSendException::class)]
final class MessageSendExceptionTest extends AbstractExceptionTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testCanBeInstantiated(): void
    {
        $exception = new MessageSendException('Test message');

        $this->assertInstanceOf(MessageSendException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertSame('Test message', $exception->getMessage());
    }

    public function testWithCodeAndPrevious(): void
    {
        $previous = new \InvalidArgumentException('Previous exception');
        $exception = new MessageSendException('Test message', '123', $previous);

        $this->assertSame('Chat 123: Test message', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testDefaultValues(): void
    {
        $exception = new MessageSendException('');

        $this->assertSame('', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }
}
