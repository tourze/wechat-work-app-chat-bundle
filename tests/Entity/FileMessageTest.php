<?php

namespace WechatWorkAppChatBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Entity\FileMessage;

class FileMessageTest extends TestCase
{
    private FileMessage $fileMessage;
    private AppChat $appChat;

    protected function setUp(): void
    {
        $this->appChat = $this->createMock(AppChat::class);
        $this->fileMessage = new FileMessage();
    }

    public function testGetMsgType(): void
    {
        $this->assertEquals('file', $this->fileMessage->getMsgType());
    }

    public function testSetAndGetMediaId(): void
    {
        $mediaId = 'test_media_id_123';
        $this->fileMessage->setMediaId($mediaId);
        $this->assertEquals($mediaId, $this->fileMessage->getMediaId());
    }

    public function testGetRequestContent(): void
    {
        $mediaId = 'test_media_id_123';
        $this->fileMessage->setMediaId($mediaId);
        
        $expected = [
            'file' => [
                'media_id' => $mediaId,
            ],
        ];
        
        $this->assertEquals($expected, $this->fileMessage->getRequestContent());
    }

    public function testSetAndGetAppChat(): void
    {
        $this->fileMessage->setAppChat($this->appChat);
        $this->assertSame($this->appChat, $this->fileMessage->getAppChat());
    }

    public function testSetAndGetIsSent(): void
    {
        $this->fileMessage->setIsSent(true);
        $this->assertTrue($this->fileMessage->isSent());
        
        $this->fileMessage->setIsSent(false);
        $this->assertFalse($this->fileMessage->isSent());
    }

    public function testSetAndGetSentAt(): void
    {
        $now = new \DateTimeImmutable();
        $this->fileMessage->setSentAt($now);
        $this->assertSame($now, $this->fileMessage->getSentAt());
    }

    public function testSetAndGetMsgId(): void
    {
        $msgId = 'test_msg_id_123';
        $this->fileMessage->setMsgId($msgId);
        $this->assertEquals($msgId, $this->fileMessage->getMsgId());
    }

    public function testSetAndGetIsRecalled(): void
    {
        $this->fileMessage->setIsRecalled(true);
        $this->assertTrue($this->fileMessage->isRecalled());
        
        $this->fileMessage->setIsRecalled(false);
        $this->assertFalse($this->fileMessage->isRecalled());
    }

    public function testSetAndGetRecalledAt(): void
    {
        $now = new \DateTimeImmutable();
        $this->fileMessage->setRecalledAt($now);
        $this->assertSame($now, $this->fileMessage->getRecalledAt());
    }
} 