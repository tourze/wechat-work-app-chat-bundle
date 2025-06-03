<?php

namespace WechatWorkAppChatBundle\Tests\Entity;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Entity\ImageMessage;

class ImageMessageTest extends TestCase
{
    private ImageMessage $imageMessage;
    private MockObject $appChat;

    protected function setUp(): void
    {
        $this->imageMessage = new ImageMessage();
        $this->appChat = $this->createMock(AppChat::class);
        $this->appChat->expects($this->any())->method('getChatId')->willReturn('test_chat_id');
    }

    public function test_getMsgType_returnsImage(): void
    {
        $this->assertEquals('image', $this->imageMessage->getMsgType());
    }

    public function test_getRequestContent_withValidMediaId(): void
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

    public function test_getRequestContent_withEmptyMediaId(): void
    {
        $this->imageMessage->setMediaId('');

        $expected = [
            'image' => [
                'media_id' => '',
            ],
        ];

        $this->assertEquals($expected, $this->imageMessage->getRequestContent());
    }

    public function test_setMediaId_andGetMediaId(): void
    {
        $mediaId = 'test_media_id_456';
        $this->imageMessage->setMediaId($mediaId);

        $this->assertEquals($mediaId, $this->imageMessage->getMediaId());
    }

    public function test_setMediaId_withLongId(): void
    {
        $mediaId = str_repeat('a', 128);
        $this->imageMessage->setMediaId($mediaId);

        $this->assertEquals($mediaId, $this->imageMessage->getMediaId());
    }

    public function test_setMediaId_withSpecialCharacters(): void
    {
        $mediaId = 'media_id_with-special_chars.123';
        $this->imageMessage->setMediaId($mediaId);

        $this->assertEquals($mediaId, $this->imageMessage->getMediaId());
    }

    public function test_inheritanceFromBaseChatMessage(): void
    {
        /** @var AppChat $appChat */
        $appChat = $this->appChat;
        $this->imageMessage->setAppChat($appChat);
        $this->imageMessage->setIsSent(true);
        $this->imageMessage->setMsgId('img_msg_123');
        $sentAt = new \DateTimeImmutable();
        $this->imageMessage->setSentAt($sentAt);

        $this->assertEquals($appChat, $this->imageMessage->getAppChat());
        $this->assertTrue($this->imageMessage->isSent());
        $this->assertEquals('img_msg_123', $this->imageMessage->getMsgId());
        $this->assertEquals($sentAt, $this->imageMessage->getSentAt());
    }
} 