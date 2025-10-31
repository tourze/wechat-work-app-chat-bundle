<?php

namespace WechatWorkAppChatBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Entity\FileMessage;
use WechatWorkAppChatBundle\Entity\ImageMessage;
use WechatWorkAppChatBundle\Entity\MarkdownMessage;
use WechatWorkAppChatBundle\Entity\TextMessage;
use WechatWorkAppChatBundle\Service\MessageService;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;

/**
 * @internal
 */
#[CoversClass(MessageService::class)]
#[RunTestsInSeparateProcesses]
final class MessageServiceTest extends AbstractIntegrationTestCase
{
    private MessageService $messageService;

    private AgentInterface $agent;

    private AppChat $appChat;

    public function testSendText(): void
    {
        $content = '测试文本消息';

        $result = $this->messageService->sendText($this->appChat, $content);

        $this->assertInstanceOf(TextMessage::class, $result);
        $this->assertSame($this->appChat, $result->getAppChat());
        $this->assertSame($content, $result->getContent());
    }

    public function testSendMarkdown(): void
    {
        $content = '# 标题\n\n这是**加粗**内容';

        $result = $this->messageService->sendMarkdown($this->appChat, $content);

        $this->assertInstanceOf(MarkdownMessage::class, $result);
        $this->assertSame($this->appChat, $result->getAppChat());
        $this->assertSame($content, $result->getContent());
    }

    public function testSendImage(): void
    {
        $mediaId = 'image_media_123';

        $result = $this->messageService->sendImage($this->appChat, $mediaId);

        $this->assertInstanceOf(ImageMessage::class, $result);
        $this->assertSame($this->appChat, $result->getAppChat());
        $this->assertSame($mediaId, $result->getMediaId());
    }

    public function testSendFile(): void
    {
        $mediaId = 'file_media_456';

        $result = $this->messageService->sendFile($this->appChat, $mediaId);

        $this->assertInstanceOf(FileMessage::class, $result);
        $this->assertSame($this->appChat, $result->getAppChat());
        $this->assertSame($mediaId, $result->getMediaId());
    }

    public function testSendUnsentWithEmptyResults(): void
    {
        $this->expectNotToPerformAssertions();

        $this->messageService->sendUnsent();
    }

    public function testSendUnsentWithSuccessfulMessages(): void
    {
        $textMessage = new TextMessage();
        $textMessage->setAppChat($this->appChat);
        $textMessage->setContent('测试文本');

        $markdownMessage = new MarkdownMessage();
        $markdownMessage->setAppChat($this->appChat);
        $markdownMessage->setContent('# 测试Markdown');

        // Persist test data
        $em = self::getEntityManager();
        $em->persist($textMessage);
        $em->persist($markdownMessage);
        $em->flush();

        $this->messageService->sendUnsent();

        $textMsgId = $textMessage->getMsgId();
        $this->assertNotNull($textMsgId);
        $this->assertStringStartsWith('mock_msg_id_', $textMsgId);
        $this->assertTrue($textMessage->isSent());
        $this->assertInstanceOf(\DateTimeImmutable::class, $textMessage->getSentAt());

        $markdownMsgId = $markdownMessage->getMsgId();
        $this->assertNotNull($markdownMsgId);
        $this->assertStringStartsWith('mock_msg_id_', $markdownMsgId);
        $this->assertTrue($markdownMessage->isSent());
        $this->assertInstanceOf(\DateTimeImmutable::class, $markdownMessage->getSentAt());
    }

    public function testSendUnsentWithAllMessageTypes(): void
    {
        $textMessage = new TextMessage();
        $textMessage->setAppChat($this->appChat);
        $textMessage->setContent('文本消息');

        $markdownMessage = new MarkdownMessage();
        $markdownMessage->setAppChat($this->appChat);
        $markdownMessage->setContent('# Markdown消息');

        $imageMessage = new ImageMessage();
        $imageMessage->setAppChat($this->appChat);
        $imageMessage->setMediaId('image_123');

        $fileMessage = new FileMessage();
        $fileMessage->setAppChat($this->appChat);
        $fileMessage->setMediaId('file_456');

        // Persist test data
        $em = self::getEntityManager();
        $em->persist($textMessage);
        $em->persist($markdownMessage);
        $em->persist($imageMessage);
        $em->persist($fileMessage);
        $em->flush();

        $this->messageService->sendUnsent();

        $textMsgId = $textMessage->getMsgId();
        $this->assertNotNull($textMsgId);
        $this->assertStringStartsWith('mock_msg_id_', $textMsgId);

        $markdownMsgId = $markdownMessage->getMsgId();
        $this->assertNotNull($markdownMsgId);
        $this->assertStringStartsWith('mock_msg_id_', $markdownMsgId);

        $imageMsgId = $imageMessage->getMsgId();
        $this->assertNotNull($imageMsgId);
        $this->assertStringStartsWith('mock_msg_id_', $imageMsgId);

        $fileMsgId = $fileMessage->getMsgId();
        $this->assertNotNull($fileMsgId);
        $this->assertStringStartsWith('mock_msg_id_', $fileMsgId);
    }

    protected function onSetUp(): void
    {
        $this->messageService = self::getService(MessageService::class);

        $this->agent = $this->createTestAgent();

        $this->appChat = new AppChat();
        $this->appChat->setAgent($this->agent);
        $this->appChat->setChatId('test_chat');
        $this->appChat->setName('测试群聊');
        $this->appChat->setOwner('owner');

        // Persist the entities for database relationship constraints
        $em = self::getEntityManager();
        $em->persist($this->agent);
        $em->persist($this->appChat);
        $em->flush();
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
