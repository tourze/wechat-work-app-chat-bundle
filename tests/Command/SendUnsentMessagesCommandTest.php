<?php

namespace WechatWorkAppChatBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;
use WechatWorkAppChatBundle\Command\SendUnsentMessagesCommand;
use WechatWorkAppChatBundle\Service\MessageService;

/**
 * @internal
 */
#[CoversClass(SendUnsentMessagesCommand::class)]
#[RunTestsInSeparateProcesses]
final class SendUnsentMessagesCommandTest extends AbstractCommandTestCase
{
    /** @var MockObject&MessageService */
    private MessageService $messageService;

    private SendUnsentMessagesCommand $command;

    public function testCommandName(): void
    {
        $this->assertSame('wechat-work:app-chat:send-unsent', SendUnsentMessagesCommand::NAME);
        $this->assertSame('wechat-work:app-chat:send-unsent', $this->command->getName());
    }

    public function testCommandDescription(): void
    {
        $this->assertSame('发送未发送的企业微信群聊消息', $this->command->getDescription());
    }

    public function testSuccessfulExecution(): void
    {
        $this->messageService
            ->expects($this->once())
            ->method('sendUnsent')
        ;

        $commandTester = new CommandTester($this->command);
        $exitCode = $commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $exitCode);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('开始发送未发送的企业微信群聊消息...', $output);
        $this->assertStringContainsString('发送完成', $output);
    }

    public function testFailedExecution(): void
    {
        $exception = new \RuntimeException('发送失败');

        $this->messageService
            ->expects($this->once())
            ->method('sendUnsent')
            ->willThrowException($exception)
        ;

        $commandTester = new CommandTester($this->command);
        $exitCode = $commandTester->execute([]);

        $this->assertSame(Command::FAILURE, $exitCode);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('开始发送未发送的企业微信群聊消息...', $output);
        $this->assertStringContainsString('发送失败：发送失败', $output);
    }

    public function testCommandWithDifferentExceptionTypes(): void
    {
        $exception = new \InvalidArgumentException('无效参数');

        $this->messageService
            ->expects($this->once())
            ->method('sendUnsent')
            ->willThrowException($exception)
        ;

        $commandTester = new CommandTester($this->command);
        $exitCode = $commandTester->execute([]);

        $this->assertSame(Command::FAILURE, $exitCode);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('发送失败：无效参数', $output);
    }

    public function testCommandWithEmptyExceptionMessage(): void
    {
        $exception = new \Exception('');

        $this->messageService
            ->expects($this->once())
            ->method('sendUnsent')
            ->willThrowException($exception)
        ;

        $commandTester = new CommandTester($this->command);
        $exitCode = $commandTester->execute([]);

        $this->assertSame(Command::FAILURE, $exitCode);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('发送失败：', $output);
    }

    public function testCommandIsInstanceOfCommand(): void
    {
        $this->assertInstanceOf(Command::class, $this->command);
    }

    protected function getCommandTester(): CommandTester
    {
        return new CommandTester(self::getService(SendUnsentMessagesCommand::class));
    }

    protected function onSetUp(): void
    {
        $this->messageService = $this->createMock(MessageService::class);
        self::getContainer()->set(MessageService::class, $this->messageService);
        $this->command = self::getService(SendUnsentMessagesCommand::class);
    }
}
