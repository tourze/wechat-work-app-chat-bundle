<?php

namespace WechatWorkAppChatBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;
use WechatWorkAppChatBundle\Command\SyncAppChatCommand;
use WechatWorkAppChatBundle\Service\AppChatService;

/**
 * @internal
 */
#[CoversClass(SyncAppChatCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncAppChatCommandTest extends AbstractCommandTestCase
{
    private MockObject $appChatService;

    private SyncAppChatCommand $command;

    private CommandTester $commandTester;

    public function testExecuteSuccess(): void
    {
        $this->appChatService
            ->expects($this->once())
            ->method('syncUnsynced')
        ;

        $result = $this->commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $result);
        $this->assertStringContainsString('开始同步企业微信群聊信息...', $this->commandTester->getDisplay());
        $this->assertStringContainsString('同步完成', $this->commandTester->getDisplay());
    }

    public function testExecuteFailure(): void
    {
        $exception = new \RuntimeException('同步失败测试');

        $this->appChatService
            ->expects($this->once())
            ->method('syncUnsynced')
            ->willThrowException($exception)
        ;

        $result = $this->commandTester->execute([]);

        $this->assertSame(Command::FAILURE, $result);
        $this->assertStringContainsString('开始同步企业微信群聊信息...', $this->commandTester->getDisplay());
        $this->assertStringContainsString('同步失败：同步失败测试', $this->commandTester->getDisplay());
    }

    public function testCommandConfiguration(): void
    {
        $this->assertSame('wechat-work:app-chat:sync', $this->command->getName());
        $this->assertSame('同步企业微信群聊信息', $this->command->getDescription());
    }

    protected function getCommandTester(): CommandTester
    {
        return new CommandTester(self::getService(SyncAppChatCommand::class));
    }

    protected function onSetUp(): void
    {
        $this->appChatService = $this->createMock(AppChatService::class);
        self::getContainer()->set(AppChatService::class, $this->appChatService);
        $this->command = self::getService(SyncAppChatCommand::class);
        $this->commandTester = new CommandTester($this->command);
    }
}
