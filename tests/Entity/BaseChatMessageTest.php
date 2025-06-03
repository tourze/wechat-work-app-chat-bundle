<?php

namespace WechatWorkAppChatBundle\Tests\Entity;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Entity\BaseChatMessage;

/**
 * 测试用的具体消息类，继承自BaseChatMessage
 */
class TestChatMessage extends BaseChatMessage
{
    public function getMsgType(): string
    {
        return 'test';
    }

    public function getRequestContent(): array
    {
        return ['test' => ['content' => 'test message']];
    }
}

class BaseChatMessageTest extends TestCase
{
    private TestChatMessage $message;
    private MockObject $appChat;

    protected function setUp(): void
    {
        $this->message = new TestChatMessage();
        $this->appChat = $this->createMock(AppChat::class);
        $this->appChat->expects($this->any())->method('getChatId')->willReturn('test_chat_id');
    }

    public function test_setAppChat_andGetAppChat(): void
    {
        /** @var AppChat $appChat */
        $appChat = $this->appChat;
        $this->message->setAppChat($appChat);

        $this->assertEquals($appChat, $this->message->getAppChat());
    }

    public function test_setIsSent_andIsSent(): void
    {
        $this->message->setIsSent(true);
        $this->assertTrue($this->message->isSent());

        $this->message->setIsSent(false);
        $this->assertFalse($this->message->isSent());
    }

    public function test_isSent_defaultValue(): void
    {
        $this->assertFalse($this->message->isSent());
    }

    public function test_setSentAt_andGetSentAt(): void
    {
        $sentAt = new \DateTimeImmutable();
        $this->message->setSentAt($sentAt);

        $this->assertEquals($sentAt, $this->message->getSentAt());
    }

    public function test_setSentAt_withNull(): void
    {
        $this->message->setSentAt(null);

        $this->assertNull($this->message->getSentAt());
    }

    public function test_setMsgId_andGetMsgId(): void
    {
        $msgId = 'test_msg_123456';
        $this->message->setMsgId($msgId);

        $this->assertEquals($msgId, $this->message->getMsgId());
    }

    public function test_setMsgId_withNull(): void
    {
        $this->message->setMsgId(null);

        $this->assertNull($this->message->getMsgId());
    }

    public function test_setIsRecalled_andIsRecalled(): void
    {
        $this->message->setIsRecalled(true);
        $this->assertTrue($this->message->isRecalled());

        $this->message->setIsRecalled(false);
        $this->assertFalse($this->message->isRecalled());
    }

    public function test_isRecalled_defaultValue(): void
    {
        $this->assertFalse($this->message->isRecalled());
    }

    public function test_setRecalledAt_andGetRecalledAt(): void
    {
        $recalledAt = new \DateTimeImmutable();
        $this->message->setRecalledAt($recalledAt);

        $this->assertEquals($recalledAt, $this->message->getRecalledAt());
    }

    public function test_setRecalledAt_withNull(): void
    {
        $this->message->setRecalledAt(null);

        $this->assertNull($this->message->getRecalledAt());
    }

    public function test_setCreatedBy_andGetCreatedBy(): void
    {
        $createdBy = 'test_creator';
        $this->message->setCreatedBy($createdBy);

        $this->assertEquals($createdBy, $this->message->getCreatedBy());
    }

    public function test_setUpdatedBy_andGetUpdatedBy(): void
    {
        $updatedBy = 'test_updater';
        $this->message->setUpdatedBy($updatedBy);

        $this->assertEquals($updatedBy, $this->message->getUpdatedBy());
    }

    public function test_setCreateTime_andGetCreateTime(): void
    {
        $createTime = new \DateTime();
        $this->message->setCreateTime($createTime);

        $this->assertEquals($createTime, $this->message->getCreateTime());
    }

    public function test_setUpdateTime_andGetUpdateTime(): void
    {
        $updateTime = new \DateTime();
        $this->message->setUpdateTime($updateTime);

        $this->assertEquals($updateTime, $this->message->getUpdateTime());
    }

    public function test_getId_defaultNull(): void
    {
        $this->assertNull($this->message->getId());
    }

    public function test_abstract_methods_are_implemented(): void
    {
        $this->assertEquals('test', $this->message->getMsgType());
        $this->assertEquals(['test' => ['content' => 'test message']], $this->message->getRequestContent());
    }

    public function test_fluent_interface(): void
    {
        /** @var AppChat $appChat */
        $appChat = $this->appChat;
        $sentAt = new \DateTimeImmutable();
        
        $result = $this->message
            ->setAppChat($appChat)
            ->setIsSent(true)
            ->setMsgId('msg_123')
            ->setSentAt($sentAt)
            ->setIsRecalled(false);

        $this->assertSame($this->message, $result);
        $this->assertEquals($appChat, $this->message->getAppChat());
        $this->assertTrue($this->message->isSent());
        $this->assertEquals('msg_123', $this->message->getMsgId());
        $this->assertEquals($sentAt, $this->message->getSentAt());
        $this->assertFalse($this->message->isRecalled());
    }
} 