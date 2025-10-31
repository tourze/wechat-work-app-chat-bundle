<?php

namespace WechatWorkAppChatBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Service\AppChatService;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;

/**
 * @internal
 */
#[CoversClass(AppChatService::class)]
#[RunTestsInSeparateProcesses]
final class AppChatServiceTest extends AbstractIntegrationTestCase
{
    private AppChatService $appChatService;

    public function testCreateAppChat(): void
    {
        $agent = $this->createTestAgent();

        $appChat = $this->appChatService->createAppChat(
            $agent,
            '测试群聊',
            'owner_id',
            ['user1', 'user2', 'user3']
        );

        $this->assertInstanceOf(AppChat::class, $appChat);
        $this->assertStringStartsWith('mock_chat_id_', $appChat->getChatId());
        $this->assertSame('测试群聊', $appChat->getName());
        $this->assertSame('owner_id', $appChat->getOwner());
        $this->assertSame(['user1', 'user2', 'user3'], $appChat->getUserList());
        $this->assertTrue($appChat->isSynced());
        $this->assertInstanceOf(\DateTimeImmutable::class, $appChat->getLastSyncedAt());
    }

    public function testUpdateAppChat(): void
    {
        $agent = $this->createTestAgent();

        $appChat = new AppChat();
        $appChat->setAgent($agent);
        $appChat->setChatId('existing_chat_id');
        $appChat->setName('群聊名称');
        $appChat->setOwner('owner_id');
        $appChat->setIsSynced(false);

        $this->appChatService->updateAppChat($appChat);

        $this->assertTrue($appChat->isSynced());
        $this->assertInstanceOf(\DateTimeImmutable::class, $appChat->getLastSyncedAt());
    }

    public function testSyncAppChatSuccess(): void
    {
        $agent = $this->createTestAgent();

        $appChat = new AppChat();
        $appChat->setAgent($agent);
        $appChat->setChatId('sync_chat_id');
        $appChat->setName('旧名称');
        $appChat->setOwner('old_owner');
        $appChat->setUserList(['old_user']);
        $appChat->setIsSynced(false);

        $this->appChatService->syncAppChat($appChat);

        $this->assertSame('Mock Chat', $appChat->getName());
        $this->assertSame('mock_owner', $appChat->getOwner());
        $this->assertSame(['user1', 'user2'], $appChat->getUserList());
        $this->assertTrue($appChat->isSynced());
        $this->assertInstanceOf(\DateTimeImmutable::class, $appChat->getLastSyncedAt());
    }

    public function testSyncUnsynced(): void
    {
        $agent = $this->createTestAgent();

        $appChat1 = new AppChat();
        $appChat1->setAgent($agent);
        $appChat1->setChatId('unsynced_1');
        $appChat1->setName('未同步群聊1');
        $appChat1->setOwner('owner1');
        $appChat1->setIsSynced(false);

        $appChat2 = new AppChat();
        $appChat2->setAgent($agent);
        $appChat2->setChatId('unsynced_2');
        $appChat2->setName('未同步群聊2');
        $appChat2->setOwner('owner2');
        $appChat2->setIsSynced(false);

        // Persist test data
        $em = self::getEntityManager();
        $em->persist($agent);  // Need to persist agent first
        $em->persist($appChat1);
        $em->persist($appChat2);
        $em->flush();

        $this->appChatService->syncUnsynced();

        $this->assertTrue($appChat1->isSynced());
        $this->assertTrue($appChat2->isSynced());
        $this->assertSame('Mock Chat', $appChat1->getName());
        $this->assertSame('Mock Chat', $appChat2->getName());
    }

    protected function onSetUp(): void
    {
        $this->appChatService = self::getService(AppChatService::class);
    }

    private function createTestAgent(): AgentInterface
    {
        $corp = new Corp();
        $corp->setName('Test Corp ' . uniqid());
        $corp->setCorpId('test_corp_id_' . uniqid());
        $corp->setCorpSecret('test_corp_secret');
        self::getEntityManager()->persist($corp);

        $agent = new Agent();
        $agent->setName('Test Agent ' . uniqid());
        $agent->setAgentId('test_agent_' . uniqid());
        $agent->setSecret('test_secret');
        $agent->setCorp($corp);
        self::getEntityManager()->persist($agent);

        return $agent;
    }
}
