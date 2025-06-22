<?php

namespace WechatWorkAppChatBundle\Tests\Entity;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Entity\FileMessage;

class FileMessageTest extends TestCase
{
    private FileMessage $fileMessage;
    private MockObject $appChat;

    protected function setUp(): void
    {
        $this->fileMessage = new FileMessage();
        $this->appChat = $this->createMock(AppChat::class);
        $this->appChat->expects($this->any())->method('getChatId')->willReturn('test_chat_id');
    }

    public function test_getMsgType_returnsFile(): void
    {
        $this->assertEquals('file', $this->fileMessage->getMsgType());
    }

    public function test_getRequestContent_withValidMediaId(): void
    {
        $mediaId = 'file_media_id_789';
        $this->fileMessage->setMediaId($mediaId);

        $expected = [
            'file' => [
                'media_id' => $mediaId,
            ],
        ];

        $this->assertEquals($expected, $this->fileMessage->getRequestContent());
    }

    public function test_getRequestContent_withEmptyMediaId(): void
    {
        $this->fileMessage->setMediaId('');

        $expected = [
            'file' => [
                'media_id' => '',
            ],
        ];

        $this->assertEquals($expected, $this->fileMessage->getRequestContent());
    }

    public function test_setMediaId_andGetMediaId(): void
    {
        $mediaId = 'file_media_id_abc';
        $this->fileMessage->setMediaId($mediaId);

        $this->assertEquals($mediaId, $this->fileMessage->getMediaId());
    }

    public function test_setMediaId_withLongId(): void
    {
        $mediaId = str_repeat('f', 128);
        $this->fileMessage->setMediaId($mediaId);

        $this->assertEquals($mediaId, $this->fileMessage->getMediaId());
    }

    public function test_setMediaId_withNumericId(): void
    {
        $mediaId = '1234567890';
        $this->fileMessage->setMediaId($mediaId);

        $this->assertEquals($mediaId, $this->fileMessage->getMediaId());
    }

    public function test_inheritanceFromBaseChatMessage(): void
    {
        /** @var MockObject&AppChat $appChat */
        $appChat = $this->appChat;
        $this->fileMessage->setAppChat($appChat);
        $this->fileMessage->setIsSent(false);
        $this->fileMessage->setIsRecalled(true);
        $recalledAt = new \DateTimeImmutable();
        $this->fileMessage->setRecalledAt($recalledAt);

        $this->assertEquals($appChat, $this->fileMessage->getAppChat());
        $this->assertFalse($this->fileMessage->isSent());
        $this->assertTrue($this->fileMessage->isRecalled());
        $this->assertEquals($recalledAt, $this->fileMessage->getRecalledAt());
    }
} 