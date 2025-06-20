<?php

namespace WechatWorkAppChatBundle\Tests\Entity;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkAppChatBundle\Entity\AppChat;

class AppChatTest extends TestCase
{
    private AppChat $appChat;
    private MockObject $agent;

    protected function setUp(): void
    {
        $this->appChat = new AppChat();
        $this->agent = $this->createMock(AgentInterface::class);
        $this->agent->expects($this->any())->method('getAgentId')->willReturn('test_agent_id');
    }

    public function test_setAgent_andGetAgent(): void
    {
        /** @var AgentInterface $agent */
        $agent = $this->agent;
        $this->appChat->setAgent($agent);

        $this->assertEquals($agent, $this->appChat->getAgent());
    }

    public function test_setChatId_andGetChatId(): void
    {
        $chatId = 'test_chat_12345';
        $this->appChat->setChatId($chatId);

        $this->assertEquals($chatId, $this->appChat->getChatId());
    }

    public function test_setChatId_withSpecialCharacters(): void
    {
        $chatId = 'chat-id_with.special123';
        $this->appChat->setChatId($chatId);

        $this->assertEquals($chatId, $this->appChat->getChatId());
    }

    public function test_setName_andGetName(): void
    {
        $name = '测试群聊名称';
        $this->appChat->setName($name);

        $this->assertEquals($name, $this->appChat->getName());
    }

    public function test_setName_withEmptyString(): void
    {
        $this->appChat->setName('');

        $this->assertEquals('', $this->appChat->getName());
    }

    public function test_setOwner_andGetOwner(): void
    {
        $owner = 'test_user_owner';
        $this->appChat->setOwner($owner);

        $this->assertEquals($owner, $this->appChat->getOwner());
    }

    public function test_setUserList_andGetUserList(): void
    {
        $userList = ['user1', 'user2', 'user3'];
        $this->appChat->setUserList($userList);

        $this->assertEquals($userList, $this->appChat->getUserList());
    }

    public function test_setUserList_withEmptyArray(): void
    {
        $this->appChat->setUserList([]);

        $this->assertEquals([], $this->appChat->getUserList());
    }

    public function test_setUserList_withSingleUser(): void
    {
        $userList = ['single_user'];
        $this->appChat->setUserList($userList);

        $this->assertEquals($userList, $this->appChat->getUserList());
    }

    public function test_setIsSynced_andIsSynced(): void
    {
        $this->appChat->setIsSynced(true);
        $this->assertTrue($this->appChat->isSynced());

        $this->appChat->setIsSynced(false);
        $this->assertFalse($this->appChat->isSynced());
    }

    public function test_isSynced_defaultValue(): void
    {
        $this->assertFalse($this->appChat->isSynced());
    }

    public function test_setLastSyncedAt_andGetLastSyncedAt(): void
    {
        $syncTime = new \DateTimeImmutable();
        $this->appChat->setLastSyncedAt($syncTime);

        $this->assertEquals($syncTime, $this->appChat->getLastSyncedAt());
    }

    public function test_setLastSyncedAt_withNull(): void
    {
        $this->appChat->setLastSyncedAt(null);

        $this->assertNull($this->appChat->getLastSyncedAt());
    }

    public function test_setCreatedBy_andGetCreatedBy(): void
    {
        $createdBy = 'test_creator';
        $this->appChat->setCreatedBy($createdBy);

        $this->assertEquals($createdBy, $this->appChat->getCreatedBy());
    }

    public function test_setUpdatedBy_andGetUpdatedBy(): void
    {
        $updatedBy = 'test_updater';
        $this->appChat->setUpdatedBy($updatedBy);

        $this->assertEquals($updatedBy, $this->appChat->getUpdatedBy());
    }

    public function test_setCreateTime_andGetCreateTime(): void
    {
        $createTime = new \DateTimeImmutable();
        $this->appChat->setCreateTime($createTime);

        $this->assertEquals($createTime, $this->appChat->getCreateTime());
    }

    public function test_setUpdateTime_andGetUpdateTime(): void
    {
        $updateTime = new \DateTimeImmutable();
        $this->appChat->setUpdateTime($updateTime);

        $this->assertEquals($updateTime, $this->appChat->getUpdateTime());
    }

    public function test_getId_defaultNull(): void
    {
        $this->assertNull($this->appChat->getId());
    }

    public function test_fluent_interface(): void
    {
        /** @var AgentInterface $agent */
        $agent = $this->agent;
        $result = $this->appChat
            ->setAgent($agent)
            ->setChatId('test_chat')
            ->setName('Test Chat')
            ->setOwner('owner')
            ->setUserList(['user1', 'user2'])
            ->setIsSynced(true);

        $this->assertSame($this->appChat, $result);
        $this->assertEquals($agent, $this->appChat->getAgent());
        $this->assertEquals('test_chat', $this->appChat->getChatId());
        $this->assertEquals('Test Chat', $this->appChat->getName());
        $this->assertEquals('owner', $this->appChat->getOwner());
        $this->assertEquals(['user1', 'user2'], $this->appChat->getUserList());
        $this->assertTrue($this->appChat->isSynced());
    }
} 