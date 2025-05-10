<?php

namespace WechatWorkAppChatBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Entity\TextMessage;

class TextMessageTest extends TestCase
{
    private TextMessage $textMessage;
    private AppChat $appChat;

    protected function setUp(): void
    {
        $this->appChat = $this->createMock(AppChat::class);
        $this->textMessage = new TextMessage();
    }

    public function testGetMsgType(): void
    {
        $this->assertEquals('text', $this->textMessage->getMsgType());
    }

    public function testSetAndGetContent(): void
    {
        $content = 'Hello, this is a test message';
        $this->textMessage->setContent($content);
        $this->assertEquals($content, $this->textMessage->getContent());
    }

    public function testGetRequestContent(): void
    {
        $content = 'Test message content';
        $this->textMessage->setContent($content);
        
        $expected = [
            'text' => [
                'content' => $content,
            ],
        ];
        
        $this->assertEquals($expected, $this->textMessage->getRequestContent());
    }

    public function testSetAndGetAppChat(): void
    {
        $this->textMessage->setAppChat($this->appChat);
        $this->assertSame($this->appChat, $this->textMessage->getAppChat());
    }

    public function testSetAndGetIsSent(): void
    {
        $this->textMessage->setIsSent(true);
        $this->assertTrue($this->textMessage->isSent());
        
        $this->textMessage->setIsSent(false);
        $this->assertFalse($this->textMessage->isSent());
    }

    public function testSetAndGetSentAt(): void
    {
        $now = new \DateTimeImmutable();
        $this->textMessage->setSentAt($now);
        $this->assertSame($now, $this->textMessage->getSentAt());
    }

    public function testSetAndGetMsgId(): void
    {
        $msgId = 'test_msg_id_123';
        $this->textMessage->setMsgId($msgId);
        $this->assertEquals($msgId, $this->textMessage->getMsgId());
    }

    public function testSetAndGetIsRecalled(): void
    {
        $this->textMessage->setIsRecalled(true);
        $this->assertTrue($this->textMessage->isRecalled());
        
        $this->textMessage->setIsRecalled(false);
        $this->assertFalse($this->textMessage->isRecalled());
    }

    public function testSetAndGetRecalledAt(): void
    {
        $now = new \DateTimeImmutable();
        $this->textMessage->setRecalledAt($now);
        $this->assertSame($now, $this->textMessage->getRecalledAt());
    }
} 