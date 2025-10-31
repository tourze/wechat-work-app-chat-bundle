<?php

namespace WechatWorkAppChatBundle\Tests\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostPersistEventArgs;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Entity\FileMessage;
use WechatWorkAppChatBundle\Entity\ImageMessage;
use WechatWorkAppChatBundle\Entity\MarkdownMessage;
use WechatWorkAppChatBundle\Entity\TextMessage;
use WechatWorkAppChatBundle\EventSubscriber\SendMessageListener;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;

/**
 * @internal
 */
#[CoversClass(SendMessageListener::class)]
#[RunTestsInSeparateProcesses]
final class SendMessageListenerTest extends AbstractIntegrationTestCase
{
    private SendMessageListener $listener;

    public function testSuccessfulMessageSending(): void
    {
        $agent = $this->createTestAgent();

        $appChat = new AppChat();
        $appChat->setAgent($agent);
        $appChat->setChatId('test_chat_123');
        $appChat->setName('测试群聊');
        $appChat->setOwner('owner');

        $message = new TextMessage();
        $message->setAppChat($appChat);
        $message->setContent('测试消息');

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $args = new PostPersistEventArgs($message, $entityManager);

        $entityManager
            ->expects($this->once())
            ->method('flush')
        ;

        $this->listener->postPersist($message, $args);

        $msgId = $message->getMsgId();
        $this->assertNotNull($msgId);
        $this->assertStringStartsWith('mock_msg_id_', $msgId);
        $this->assertTrue($message->isSent());
        $this->assertInstanceOf(\DateTimeImmutable::class, $message->getSentAt());
    }

    public function testMarkdownMessageSending(): void
    {
        $agent = $this->createTestAgent();

        $appChat = new AppChat();
        $appChat->setAgent($agent);
        $appChat->setChatId('test_chat_markdown');
        $appChat->setName('测试群聊');
        $appChat->setOwner('owner');

        $message = new MarkdownMessage();
        $message->setAppChat($appChat);
        $message->setContent('# 测试Markdown消息');

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $args = new PostPersistEventArgs($message, $entityManager);

        $entityManager
            ->expects($this->once())
            ->method('flush')
        ;

        $this->listener->postPersist($message, $args);

        $msgId = $message->getMsgId();
        $this->assertNotNull($msgId);
        $this->assertStringStartsWith('mock_msg_id_', $msgId);
        $this->assertTrue($message->isSent());
        $this->assertInstanceOf(\DateTimeImmutable::class, $message->getSentAt());
    }

    public function testImageMessageSending(): void
    {
        $agent = $this->createTestAgent();

        $appChat = new AppChat();
        $appChat->setAgent($agent);
        $appChat->setChatId('test_chat_image');
        $appChat->setName('测试群聊');
        $appChat->setOwner('owner');

        $message = new ImageMessage();
        $message->setAppChat($appChat);
        $message->setMediaId('image_123');

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $args = new PostPersistEventArgs($message, $entityManager);

        $entityManager
            ->expects($this->once())
            ->method('flush')
        ;

        $this->listener->postPersist($message, $args);

        $msgId = $message->getMsgId();
        $this->assertNotNull($msgId);
        $this->assertStringStartsWith('mock_msg_id_', $msgId);
        $this->assertTrue($message->isSent());
        $this->assertInstanceOf(\DateTimeImmutable::class, $message->getSentAt());
    }

    public function testFileMessageSending(): void
    {
        $agent = $this->createTestAgent();

        $appChat = new AppChat();
        $appChat->setAgent($agent);
        $appChat->setChatId('test_chat_file');
        $appChat->setName('测试群聊');
        $appChat->setOwner('owner');

        $message = new FileMessage();
        $message->setAppChat($appChat);
        $message->setMediaId('file_456');

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $args = new PostPersistEventArgs($message, $entityManager);

        $entityManager
            ->expects($this->once())
            ->method('flush')
        ;

        $this->listener->postPersist($message, $args);

        $msgId = $message->getMsgId();
        $this->assertNotNull($msgId);
        $this->assertStringStartsWith('mock_msg_id_', $msgId);
        $this->assertTrue($message->isSent());
        $this->assertInstanceOf(\DateTimeImmutable::class, $message->getSentAt());
    }

    public function testPostPersistMethodExists(): void
    {
        $this->expectNotToPerformAssertions();

        $entity = $this->createMock(TextMessage::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $event = new PostPersistEventArgs($entity, $entityManager);

        $this->listener->postPersist($entity, $event);
    }

    protected function onSetUp(): void
    {
        // Get the service from container which will use MockWorkService
        $this->listener = self::getService(SendMessageListener::class);
    }

    private function createTestAgent(): AgentInterface
    {
        $corp = new Corp();
        $corp->setName('Test Corp ' . uniqid());
        $corp->setCorpId('test_corp_id_' . uniqid());
        $corp->setCorpSecret('test_corp_secret');
        self::getEntityManager()->persist($corp);

        $agent = new Agent();
        $agent->setName('Test Agent ' . uniqid());
        $agent->setAgentId('test_agent_' . uniqid());
        $agent->setSecret('test_secret');
        $agent->setCorp($corp);
        self::getEntityManager()->persist($agent);

        return $agent;
    }
}
