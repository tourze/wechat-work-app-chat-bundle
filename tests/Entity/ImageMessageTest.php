<?php

namespace WechatWorkAppChatBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Entity\ImageMessage;

class ImageMessageTest extends TestCase
{
    private ImageMessage $imageMessage;
    private AppChat $appChat;

    protected function setUp(): void
    {
        $this->appChat = $this->createMock(AppChat::class);
        $this->imageMessage = new ImageMessage();
    }

    public function testGetMsgType(): void
    {
        $this->assertEquals('image', $this->imageMessage->getMsgType());
    }

    public function testSetAndGetMediaId(): void
    {
        $mediaId = 'test_media_id_123';
        $this->imageMessage->setMediaId($mediaId);
        $this->assertEquals($mediaId, $this->imageMessage->getMediaId());
    }

    public function testGetRequestContent(): void
    {
        $mediaId = 'test_media_id_123';
        $this->imageMessage->setMediaId($mediaId);
        
        $expected = [
            'image' => [
                'media_id' => $mediaId,
            ],
        ];
        
        $this->assertEquals($expected, $this->imageMessage->getRequestContent());
    }

    public function testSetAndGetAppChat(): void
    {
        $this->imageMessage->setAppChat($this->appChat);
        $this->assertSame($this->appChat, $this->imageMessage->getAppChat());
    }

    public function testSetAndGetIsSent(): void
    {
        $this->imageMessage->setIsSent(true);
        $this->assertTrue($this->imageMessage->isSent());
        
        $this->imageMessage->setIsSent(false);
        $this->assertFalse($this->imageMessage->isSent());
    }

    public function testSetAndGetSentAt(): void
    {
        $now = new \DateTimeImmutable();
        $this->imageMessage->setSentAt($now);
        $this->assertSame($now, $this->imageMessage->getSentAt());
    }

    public function testSetAndGetMsgId(): void
    {
        $msgId = 'test_msg_id_123';
        $this->imageMessage->setMsgId($msgId);
        $this->assertEquals($msgId, $this->imageMessage->getMsgId());
    }

    public function testSetAndGetIsRecalled(): void
    {
        $this->imageMessage->setIsRecalled(true);
        $this->assertTrue($this->imageMessage->isRecalled());
        
        $this->imageMessage->setIsRecalled(false);
        $this->assertFalse($this->imageMessage->isRecalled());
    }

    public function testSetAndGetRecalledAt(): void
    {
        $now = new \DateTimeImmutable();
        $this->imageMessage->setRecalledAt($now);
        $this->assertSame($now, $this->imageMessage->getRecalledAt());
    }
} 