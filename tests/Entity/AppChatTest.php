<?php

namespace WechatWorkAppChatBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkBundle\Entity\Agent;

class AppChatTest extends TestCase
{
    private AppChat $appChat;
    private Agent $agent;

    protected function setUp(): void
    {
        $this->agent = $this->createMock(Agent::class);
        $this->appChat = new AppChat();
    }

    public function testSetAndGetChatId(): void
    {
        $chatId = 'test_chat_id';
        $this->appChat->setChatId($chatId);
        $this->assertEquals($chatId, $this->appChat->getChatId());
    }

    public function testSetAndGetName(): void
    {
        $name = 'Test Chat Name';
        $this->appChat->setName($name);
        $this->assertEquals($name, $this->appChat->getName());
    }

    public function testSetAndGetOwner(): void
    {
        $owner = 'test_owner';
        $this->appChat->setOwner($owner);
        $this->assertEquals($owner, $this->appChat->getOwner());
    }

    public function testSetAndGetUserList(): void
    {
        $userList = ['user1', 'user2', 'user3'];
        $this->appChat->setUserList($userList);
        $this->assertEquals($userList, $this->appChat->getUserList());
    }

    public function testSetAndGetAgent(): void
    {
        $this->appChat->setAgent($this->agent);
        $this->assertSame($this->agent, $this->appChat->getAgent());
    }

    public function testSetAndGetIsSynced(): void
    {
        $this->appChat->setIsSynced(true);
        $this->assertTrue($this->appChat->isSynced());
        
        $this->appChat->setIsSynced(false);
        $this->assertFalse($this->appChat->isSynced());
    }

    public function testSetAndGetLastSyncedAt(): void
    {
        $now = new \DateTimeImmutable();
        $this->appChat->setLastSyncedAt($now);
        $this->assertSame($now, $this->appChat->getLastSyncedAt());
    }

    public function testSetAndGetCreatedBy(): void
    {
        $createdBy = 'test_user';
        $this->appChat->setCreatedBy($createdBy);
        $this->assertEquals($createdBy, $this->appChat->getCreatedBy());
    }

    public function testSetAndGetUpdatedBy(): void
    {
        $updatedBy = 'test_user';
        $this->appChat->setUpdatedBy($updatedBy);
        $this->assertEquals($updatedBy, $this->appChat->getUpdatedBy());
    }

    public function testSetAndGetCreateTime(): void
    {
        $dateTime = new \DateTime();
        $this->appChat->setCreateTime($dateTime);
        $this->assertSame($dateTime, $this->appChat->getCreateTime());
    }

    public function testSetAndGetUpdateTime(): void
    {
        $dateTime = new \DateTime();
        $this->appChat->setUpdateTime($dateTime);
        $this->assertSame($dateTime, $this->appChat->getUpdateTime());
    }
} 