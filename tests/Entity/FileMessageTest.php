<?php

namespace WechatWorkAppChatBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Entity\BaseChatMessage;
use WechatWorkAppChatBundle\Entity\FileMessage;

/**
 * @internal
 */
#[CoversClass(FileMessage::class)]
final class FileMessageTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new FileMessage();
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function propertiesProvider(): array
    {
        return [
            'mediaId' => ['mediaId', 'test_value'],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateFileMessage(): void
    {
        $agent = $this->createMock(AgentInterface::class);

        $appChat = new AppChat();
        $appChat->setAgent($agent);
        $appChat->setChatId('test_chat');
        $appChat->setName('测试群聊');
        $appChat->setOwner('owner');

        $fileMessage = new FileMessage();
        $fileMessage->setAppChat($appChat);
        $fileMessage->setMediaId('test_media_id_123');
        $fileMessage->setIsSent(true);
        $fileMessage->setSentAt(new \DateTimeImmutable());
        $fileMessage->setMsgId('test_msg_id');

        $this->assertSame($appChat, $fileMessage->getAppChat());
        $this->assertSame('test_media_id_123', $fileMessage->getMediaId());
        $this->assertTrue($fileMessage->isSent());
        $this->assertInstanceOf(\DateTimeImmutable::class, $fileMessage->getSentAt());
        $this->assertSame('test_msg_id', $fileMessage->getMsgId());
    }

    public function testGetMsgType(): void
    {
        $fileMessage = new FileMessage();
        $this->assertSame('file', $fileMessage->getMsgType());
    }

    public function testGetRequestContent(): void
    {
        $fileMessage = new FileMessage();
        $fileMessage->setMediaId('media_id_file_123');

        $expected = [
            'file' => [
                'media_id' => 'media_id_file_123',
            ],
        ];

        $this->assertSame($expected, $fileMessage->getRequestContent());
    }

    public function testFileMessageDefaults(): void
    {
        $fileMessage = new FileMessage();

        $this->assertFalse($fileMessage->isSent());
        $this->assertNull($fileMessage->getSentAt());
        $this->assertNull($fileMessage->getMsgId());
        $this->assertFalse($fileMessage->isRecalled());
        $this->assertNull($fileMessage->getRecalledAt());
    }

    public function testSettersWorkCorrectly(): void
    {
        $agent = $this->createMock(AgentInterface::class);

        $appChat = new AppChat();
        $appChat->setAgent($agent);
        $appChat->setChatId('test_chat');
        $appChat->setName('测试群聊');
        $appChat->setOwner('owner');

        $now = new \DateTimeImmutable();
        $fileMessage = new FileMessage();

        $fileMessage->setAppChat($appChat);
        $fileMessage->setMediaId('test_media_id');
        $fileMessage->setIsSent(true);
        $fileMessage->setSentAt($now);
        $fileMessage->setMsgId('msg123');
        $fileMessage->setIsRecalled(false);
        $fileMessage->setRecalledAt(null);

        $this->assertSame($appChat, $fileMessage->getAppChat());
        $this->assertSame('test_media_id', $fileMessage->getMediaId());
        $this->assertTrue($fileMessage->isSent());
        $this->assertSame($now, $fileMessage->getSentAt());
        $this->assertSame('msg123', $fileMessage->getMsgId());
        $this->assertFalse($fileMessage->isRecalled());
        $this->assertNull($fileMessage->getRecalledAt());
    }

    public function testStringable(): void
    {
        $fileMessage = new FileMessage();
        $this->assertIsString((string) $fileMessage);
    }

    public function testMediaIdValidation(): void
    {
        $fileMessage = new FileMessage();

        // Test with various media ID lengths
        $shortMediaId = 'short';
        $fileMessage->setMediaId($shortMediaId);
        $this->assertSame($shortMediaId, $fileMessage->getMediaId());

        $longMediaId = str_repeat('a', 128);
        $fileMessage->setMediaId($longMediaId);
        $this->assertSame($longMediaId, $fileMessage->getMediaId());
    }

    public function testRecallFunctionality(): void
    {
        $fileMessage = new FileMessage();
        $recallTime = new \DateTimeImmutable();

        $fileMessage->setIsRecalled(true);
        $fileMessage->setRecalledAt($recallTime);

        $this->assertTrue($fileMessage->isRecalled());
        $this->assertSame($recallTime, $fileMessage->getRecalledAt());
    }

    public function testFileMessageInheritance(): void
    {
        $fileMessage = new FileMessage();
        $this->assertInstanceOf(BaseChatMessage::class, $fileMessage);
    }
}
