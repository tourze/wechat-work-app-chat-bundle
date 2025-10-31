<?php

namespace WechatWorkAppChatBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\WechatWorkContracts\AgentInterface;
use Tourze\WechatWorkContracts\CorpInterface;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;

/**
 * @internal
 */
#[CoversClass(AppChat::class)]
final class AppChatTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new AppChat();
    }

    /**
     * @return array<string, array{string, mixed}>
     */
    public static function propertiesProvider(): array
    {
        // 使用真实Agent实体而不是匿名类
        $corp = new Corp();
        $corp->setName('Test Corp');
        $corp->setCorpId('test_corp_id');
        $corp->setCorpSecret('test_corp_secret');

        $agent = new Agent();
        $agent->setName('Test Agent');
        $agent->setAgentId('test_agent_id');
        $agent->setSecret('test_secret');
        $agent->setCorp($corp);

        return [
            'agent' => ['agent', $agent],
            'chatId' => ['chatId', 'test_value'],
            'name' => ['name', 'test_value'],
            'owner' => ['owner', 'test_value'],
            'userList' => ['userList', ['key' => 'value']],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        // 集成测试不需要额外的设置
    }

    public function testCreateAppChat(): void
    {
        $agent = $this->createMock(AgentInterface::class);

        $appChat = new AppChat();
        $appChat->setAgent($agent);
        $appChat->setChatId('test_chat_id');
        $appChat->setName('测试群聊');
        $appChat->setOwner('owner_user_id');
        $appChat->setUserList(['user1', 'user2', 'user3']);
        $appChat->setIsSynced(true);
        $appChat->setLastSyncedAt(new \DateTimeImmutable());

        $this->assertSame($agent, $appChat->getAgent());
        $this->assertSame('test_chat_id', $appChat->getChatId());
        $this->assertSame('测试群聊', $appChat->getName());
        $this->assertSame('owner_user_id', $appChat->getOwner());
        $this->assertSame(['user1', 'user2', 'user3'], $appChat->getUserList());
        $this->assertTrue($appChat->isSynced());
        $this->assertInstanceOf(\DateTimeImmutable::class, $appChat->getLastSyncedAt());
    }

    public function testAppChatStringable(): void
    {
        $agent = $this->createMock(AgentInterface::class);

        $appChat = new AppChat();
        $appChat->setAgent($agent);
        $appChat->setChatId('test_chat_id');
        $appChat->setName('测试群聊');
        $appChat->setOwner('owner_user_id');

        $this->assertIsString((string) $appChat);
    }

    public function testAppChatDefaults(): void
    {
        $appChat = new AppChat();

        $this->assertFalse($appChat->isSynced());
        $this->assertNull($appChat->getLastSyncedAt());
        $this->assertSame([], $appChat->getUserList());
    }

    public function testSettersWorkCorrectly(): void
    {
        $agent = $this->createMock(AgentInterface::class);
        $now = new \DateTimeImmutable();

        $appChat = new AppChat();

        $appChat->setAgent($agent);
        $appChat->setChatId('test_id');
        $appChat->setName('test_name');
        $appChat->setOwner('test_owner');
        $appChat->setUserList(['user1']);
        $appChat->setIsSynced(true);
        $appChat->setLastSyncedAt($now);

        $this->assertSame($agent, $appChat->getAgent());
        $this->assertSame('test_id', $appChat->getChatId());
        $this->assertSame('test_name', $appChat->getName());
        $this->assertSame('test_owner', $appChat->getOwner());
        $this->assertSame(['user1'], $appChat->getUserList());
        $this->assertTrue($appChat->isSynced());
        $this->assertSame($now, $appChat->getLastSyncedAt());
    }
}
