<?php

namespace WechatWorkAppChatBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Entity\TextMessage;

/**
 * @internal
 */
#[CoversClass(TextMessage::class)]
final class TextMessageTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new TextMessage();
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function propertiesProvider(): array
    {
        return [
            'content' => ['content', 'test_value'],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateTextMessage(): void
    {
        $agent = $this->createMock(AgentInterface::class);

        $appChat = new AppChat();
        $appChat->setAgent($agent);
        $appChat->setChatId('test_chat');
        $appChat->setName('测试群聊');
        $appChat->setOwner('owner');

        $textMessage = new TextMessage();
        $textMessage->setAppChat($appChat);
        $textMessage->setContent('这是一条测试文本消息');
        $textMessage->setIsSent(true);
        $textMessage->setSentAt(new \DateTimeImmutable());
        $textMessage->setMsgId('test_msg_id');

        $this->assertSame($appChat, $textMessage->getAppChat());
        $this->assertSame('这是一条测试文本消息', $textMessage->getContent());
        $this->assertTrue($textMessage->isSent());
        $this->assertInstanceOf(\DateTimeImmutable::class, $textMessage->getSentAt());
        $this->assertSame('test_msg_id', $textMessage->getMsgId());
    }

    public function testGetMsgType(): void
    {
        $textMessage = new TextMessage();
        $this->assertSame('text', $textMessage->getMsgType());
    }

    public function testGetRequestContent(): void
    {
        $textMessage = new TextMessage();
        $textMessage->setContent('测试消息内容');

        $expected = [
            'text' => [
                'content' => '测试消息内容',
            ],
        ];

        $this->assertSame($expected, $textMessage->getRequestContent());
    }

    public function testTextMessageDefaults(): void
    {
        $textMessage = new TextMessage();

        $this->assertFalse($textMessage->isSent());
        $this->assertNull($textMessage->getSentAt());
        $this->assertNull($textMessage->getMsgId());
        $this->assertFalse($textMessage->isRecalled());
        $this->assertNull($textMessage->getRecalledAt());
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
        $textMessage = new TextMessage();

        $textMessage->setAppChat($appChat);
        $textMessage->setContent('测试内容');
        $textMessage->setIsSent(true);
        $textMessage->setSentAt($now);
        $textMessage->setMsgId('msg123');
        $textMessage->setIsRecalled(false);
        $textMessage->setRecalledAt(null);

        $this->assertSame($appChat, $textMessage->getAppChat());
        $this->assertSame('测试内容', $textMessage->getContent());
        $this->assertTrue($textMessage->isSent());
        $this->assertSame($now, $textMessage->getSentAt());
        $this->assertSame('msg123', $textMessage->getMsgId());
        $this->assertFalse($textMessage->isRecalled());
        $this->assertNull($textMessage->getRecalledAt());
    }

    public function testStringable(): void
    {
        $textMessage = new TextMessage();
        $this->assertIsString((string) $textMessage);
    }
}
