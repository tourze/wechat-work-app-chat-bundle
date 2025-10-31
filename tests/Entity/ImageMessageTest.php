<?php

namespace WechatWorkAppChatBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Entity\BaseChatMessage;
use WechatWorkAppChatBundle\Entity\ImageMessage;

/**
 * @internal
 */
#[CoversClass(ImageMessage::class)]
final class ImageMessageTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new ImageMessage();
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

    public function testCreateImageMessage(): void
    {
        $agent = $this->createMock(AgentInterface::class);

        $appChat = new AppChat();
        $appChat->setAgent($agent);
        $appChat->setChatId('test_chat');
        $appChat->setName('测试群聊');
        $appChat->setOwner('owner');

        $imageMessage = new ImageMessage();
        $imageMessage->setAppChat($appChat);
        $imageMessage->setMediaId('test_image_media_123');
        $imageMessage->setIsSent(true);
        $imageMessage->setSentAt(new \DateTimeImmutable());
        $imageMessage->setMsgId('test_msg_id');

        $this->assertSame($appChat, $imageMessage->getAppChat());
        $this->assertSame('test_image_media_123', $imageMessage->getMediaId());
        $this->assertTrue($imageMessage->isSent());
        $this->assertInstanceOf(\DateTimeImmutable::class, $imageMessage->getSentAt());
        $this->assertSame('test_msg_id', $imageMessage->getMsgId());
    }

    public function testGetMsgType(): void
    {
        $imageMessage = new ImageMessage();
        $this->assertSame('image', $imageMessage->getMsgType());
    }

    public function testGetRequestContent(): void
    {
        $imageMessage = new ImageMessage();
        $imageMessage->setMediaId('image_media_id_456');

        $expected = [
            'image' => [
                'media_id' => 'image_media_id_456',
            ],
        ];

        $this->assertSame($expected, $imageMessage->getRequestContent());
    }

    public function testImageMessageDefaults(): void
    {
        $imageMessage = new ImageMessage();

        $this->assertFalse($imageMessage->isSent());
        $this->assertNull($imageMessage->getSentAt());
        $this->assertNull($imageMessage->getMsgId());
        $this->assertFalse($imageMessage->isRecalled());
        $this->assertNull($imageMessage->getRecalledAt());
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
        $imageMessage = new ImageMessage();

        $imageMessage->setAppChat($appChat);
        $imageMessage->setMediaId('test_image_media');
        $imageMessage->setIsSent(true);
        $imageMessage->setSentAt($now);
        $imageMessage->setMsgId('msg123');
        $imageMessage->setIsRecalled(false);
        $imageMessage->setRecalledAt(null);

        $this->assertSame($appChat, $imageMessage->getAppChat());
        $this->assertSame('test_image_media', $imageMessage->getMediaId());
        $this->assertTrue($imageMessage->isSent());
        $this->assertSame($now, $imageMessage->getSentAt());
        $this->assertSame('msg123', $imageMessage->getMsgId());
        $this->assertFalse($imageMessage->isRecalled());
        $this->assertNull($imageMessage->getRecalledAt());
    }

    public function testStringable(): void
    {
        $imageMessage = new ImageMessage();
        $this->assertIsString((string) $imageMessage);
    }

    public function testMediaIdValidation(): void
    {
        $imageMessage = new ImageMessage();

        // Test with various media ID lengths
        $shortMediaId = 'img_short';
        $imageMessage->setMediaId($shortMediaId);
        $this->assertSame($shortMediaId, $imageMessage->getMediaId());

        $longMediaId = str_repeat('img_', 32); // 128 characters
        $imageMessage->setMediaId($longMediaId);
        $this->assertSame($longMediaId, $imageMessage->getMediaId());
    }

    public function testRecallFunctionality(): void
    {
        $imageMessage = new ImageMessage();
        $recallTime = new \DateTimeImmutable();

        $imageMessage->setIsRecalled(true);
        $imageMessage->setRecalledAt($recallTime);

        $this->assertTrue($imageMessage->isRecalled());
        $this->assertSame($recallTime, $imageMessage->getRecalledAt());
    }

    public function testImageMessageInheritance(): void
    {
        $imageMessage = new ImageMessage();
        $this->assertInstanceOf(BaseChatMessage::class, $imageMessage);
    }

    public function testImageMessageIdentity(): void
    {
        $imageMessage = new ImageMessage();
        $imageMessage->setMediaId('unique_image_123');

        $requestContent = $imageMessage->getRequestContent();
        $this->assertIsArray($requestContent);
        $this->assertArrayHasKey('image', $requestContent);
        $imageData = $requestContent['image'];
        $this->assertIsArray($imageData);
        $this->assertArrayHasKey('media_id', $imageData);
        $this->assertSame('unique_image_123', $imageData['media_id']);
    }
}
