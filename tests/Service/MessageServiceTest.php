<?php

namespace WechatWorkAppChatBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Entity\FileMessage;
use WechatWorkAppChatBundle\Entity\ImageMessage;
use WechatWorkAppChatBundle\Entity\MarkdownMessage;
use WechatWorkAppChatBundle\Entity\TextMessage;
use WechatWorkAppChatBundle\Repository\FileMessageRepository;
use WechatWorkAppChatBundle\Repository\ImageMessageRepository;
use WechatWorkAppChatBundle\Repository\MarkdownMessageRepository;
use WechatWorkAppChatBundle\Repository\TextMessageRepository;
use WechatWorkAppChatBundle\Service\MessageService;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Service\WorkService;

class MessageServiceTest extends TestCase
{
    private EntityManagerInterface|MockObject $entityManager;
    private TextMessageRepository|MockObject $textMessageRepository;
    private MarkdownMessageRepository|MockObject $markdownMessageRepository;
    private ImageMessageRepository|MockObject $imageMessageRepository;
    private FileMessageRepository|MockObject $fileMessageRepository;
    private WorkService|MockObject $workService;
    private LoggerInterface|MockObject $logger;
    private MessageService $messageService;
    private AppChat $appChat;
    private Agent $agent;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->textMessageRepository = $this->createMock(TextMessageRepository::class);
        $this->markdownMessageRepository = $this->createMock(MarkdownMessageRepository::class);
        $this->imageMessageRepository = $this->createMock(ImageMessageRepository::class);
        $this->fileMessageRepository = $this->createMock(FileMessageRepository::class);
        $this->workService = $this->createMock(WorkService::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        
        $this->messageService = new MessageService(
            $this->entityManager,
            $this->textMessageRepository,
            $this->markdownMessageRepository,
            $this->imageMessageRepository,
            $this->fileMessageRepository,
            $this->workService,
            $this->logger
        );
        
        $this->agent = $this->createMock(Agent::class);
        
        $this->appChat = new AppChat();
        $this->appChat->setAgent($this->agent);
        $this->appChat->setChatId('test_chat_id');
        $this->appChat->setName('Test Chat');
        $this->appChat->setOwner('test_owner');
        $this->appChat->setUserList(['user1', 'user2']);
    }

    public function testSendText_withValidContent(): void
    {
        $content = 'Test message content';
        
        // 设置模拟期望
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (TextMessage $message) use ($content) {
                return $message->getContent() === $content
                    && $message->getAppChat() === $this->appChat;
            }));
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $result = $this->messageService->sendText($this->appChat, $content);
        
        // 断言
        $this->assertInstanceOf(TextMessage::class, $result);
        $this->assertEquals($content, $result->getContent());
        $this->assertSame($this->appChat, $result->getAppChat());
    }

    public function testSendMarkdown_withValidContent(): void
    {
        $content = '# Test Markdown';
        
        // 设置模拟期望
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (MarkdownMessage $message) use ($content) {
                return $message->getContent() === $content
                    && $message->getAppChat() === $this->appChat;
            }));
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $result = $this->messageService->sendMarkdown($this->appChat, $content);
        
        // 断言
        $this->assertInstanceOf(MarkdownMessage::class, $result);
        $this->assertEquals($content, $result->getContent());
        $this->assertSame($this->appChat, $result->getAppChat());
    }

    public function testSendImage_withValidMediaId(): void
    {
        $mediaId = 'test_media_id';
        
        // 设置模拟期望
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (ImageMessage $message) use ($mediaId) {
                return $message->getMediaId() === $mediaId
                    && $message->getAppChat() === $this->appChat;
            }));
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $result = $this->messageService->sendImage($this->appChat, $mediaId);
        
        // 断言
        $this->assertInstanceOf(ImageMessage::class, $result);
        $this->assertEquals($mediaId, $result->getMediaId());
        $this->assertSame($this->appChat, $result->getAppChat());
    }

    public function testSendFile_withValidMediaId(): void
    {
        $mediaId = 'test_media_id';
        
        // 设置模拟期望
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (FileMessage $message) use ($mediaId) {
                return $message->getMediaId() === $mediaId
                    && $message->getAppChat() === $this->appChat;
            }));
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $result = $this->messageService->sendFile($this->appChat, $mediaId);
        
        // 断言
        $this->assertInstanceOf(FileMessage::class, $result);
        $this->assertEquals($mediaId, $result->getMediaId());
        $this->assertSame($this->appChat, $result->getAppChat());
    }

    public function testSendUnsent_withMultipleMessages(): void
    {
        // 创建未发送的消息
        $textMessage = new TextMessage();
        $textMessage->setAppChat($this->appChat);
        $textMessage->setContent('Test text message');
        
        $markdownMessage = new MarkdownMessage();
        $markdownMessage->setAppChat($this->appChat);
        $markdownMessage->setContent('# Test markdown message');
        
        $imageMessage = new ImageMessage();
        $imageMessage->setAppChat($this->appChat);
        $imageMessage->setMediaId('test_image_media_id');
        
        $fileMessage = new FileMessage();
        $fileMessage->setAppChat($this->appChat);
        $fileMessage->setMediaId('test_file_media_id');
        
        $messages = [
            $textMessage,
            $markdownMessage,
            $imageMessage,
            $fileMessage,
        ];
        
        // 设置模拟期望
        $this->textMessageRepository->expects($this->once())
            ->method('findUnsent')
            ->willReturn([$textMessage]);
            
        $this->markdownMessageRepository->expects($this->once())
            ->method('findUnsent')
            ->willReturn([$markdownMessage]);
            
        $this->imageMessageRepository->expects($this->once())
            ->method('findUnsent')
            ->willReturn([$imageMessage]);
            
        $this->fileMessageRepository->expects($this->once())
            ->method('findUnsent')
            ->willReturn([$fileMessage]);
            
        $msgId = 'msg_id_123';
        $this->workService->expects($this->exactly(count($messages)))
            ->method('request')
            ->willReturn(['msgid' => $msgId]);
            
        $this->entityManager->expects($this->exactly(count($messages)))
            ->method('flush');
        
        // 执行测试
        $this->messageService->sendUnsent();
        
        // 断言
        foreach ($messages as $message) {
            $this->assertTrue($message->isSent());
            $this->assertEquals($msgId, $message->getMsgId());
            $this->assertNotNull($message->getSentAt());
        }
    }

    public function testSendUnsent_withException(): void
    {
        // 创建未发送的消息
        $textMessage = new TextMessage();
        $textMessage->setAppChat($this->appChat);
        $textMessage->setContent('Test text message');
        
        // 设置模拟期望
        $this->textMessageRepository->expects($this->once())
            ->method('findUnsent')
            ->willReturn([$textMessage]);
            
        $this->markdownMessageRepository->expects($this->once())
            ->method('findUnsent')
            ->willReturn([]);
            
        $this->imageMessageRepository->expects($this->once())
            ->method('findUnsent')
            ->willReturn([]);
            
        $this->fileMessageRepository->expects($this->once())
            ->method('findUnsent')
            ->willReturn([]);
            
        $exception = new \Exception('API Error');
        $this->workService->expects($this->once())
            ->method('request')
            ->willThrowException($exception);
            
        $this->logger->expects($this->once())
            ->method('error')
            ->with('发送群聊消息失败', $this->anything());
        
        // 执行测试 - 不应抛出异常，因为异常已被捕获和记录
        $this->messageService->sendUnsent();
        
        // 断言 - 消息状态不应改变
        $this->assertFalse($textMessage->isSent());
        $this->assertNull($textMessage->getMsgId());
        $this->assertNull($textMessage->getSentAt());
    }
} 