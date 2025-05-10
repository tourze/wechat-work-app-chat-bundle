<?php

namespace WechatWorkAppChatBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Entity\MarkdownMessage;

class MarkdownMessageTest extends TestCase
{
    private MarkdownMessage $markdownMessage;
    private AppChat $appChat;

    protected function setUp(): void
    {
        $this->appChat = $this->createMock(AppChat::class);
        $this->markdownMessage = new MarkdownMessage();
    }

    public function testGetMsgType(): void
    {
        $this->assertEquals('markdown', $this->markdownMessage->getMsgType());
    }

    public function testSetAndGetContent(): void
    {
        $content = '# Header\n**Bold text**\n> Quote';
        $this->markdownMessage->setContent($content);
        $this->assertEquals($content, $this->markdownMessage->getContent());
    }

    public function testGetRequestContent(): void
    {
        $content = '# Test Header\n- List item 1\n- List item 2';
        $this->markdownMessage->setContent($content);
        
        $expected = [
            'markdown' => [
                'content' => $content,
            ],
        ];
        
        $this->assertEquals($expected, $this->markdownMessage->getRequestContent());
    }

    public function testSetAndGetAppChat(): void
    {
        $this->markdownMessage->setAppChat($this->appChat);
        $this->assertSame($this->appChat, $this->markdownMessage->getAppChat());
    }

    public function testSetAndGetIsSent(): void
    {
        $this->markdownMessage->setIsSent(true);
        $this->assertTrue($this->markdownMessage->isSent());
        
        $this->markdownMessage->setIsSent(false);
        $this->assertFalse($this->markdownMessage->isSent());
    }

    public function testSetAndGetSentAt(): void
    {
        $now = new \DateTimeImmutable();
        $this->markdownMessage->setSentAt($now);
        $this->assertSame($now, $this->markdownMessage->getSentAt());
    }

    public function testSetAndGetMsgId(): void
    {
        $msgId = 'test_msg_id_123';
        $this->markdownMessage->setMsgId($msgId);
        $this->assertEquals($msgId, $this->markdownMessage->getMsgId());
    }

    public function testSetAndGetIsRecalled(): void
    {
        $this->markdownMessage->setIsRecalled(true);
        $this->assertTrue($this->markdownMessage->isRecalled());
        
        $this->markdownMessage->setIsRecalled(false);
        $this->assertFalse($this->markdownMessage->isRecalled());
    }

    public function testSetAndGetRecalledAt(): void
    {
        $now = new \DateTimeImmutable();
        $this->markdownMessage->setRecalledAt($now);
        $this->assertSame($now, $this->markdownMessage->getRecalledAt());
    }
} 