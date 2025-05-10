<?php

namespace WechatWorkAppChatBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Repository\AppChatRepository;
use WechatWorkAppChatBundle\Service\AppChatService;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Service\WorkService;

class AppChatServiceTest extends TestCase
{
    private EntityManagerInterface|MockObject $entityManager;
    private AppChatRepository|MockObject $appChatRepository;
    private WorkService|MockObject $workService;
    private LoggerInterface|MockObject $logger;
    private AppChatService $appChatService;
    private Agent $agent;
    private AppChat $appChat;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->appChatRepository = $this->createMock(AppChatRepository::class);
        $this->workService = $this->createMock(WorkService::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        
        $this->appChatService = new AppChatService(
            $this->entityManager,
            $this->appChatRepository,
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

    public function testCreateAppChat_withValidData(): void
    {
        $name = 'Test Chat';
        $owner = 'test_owner';
        $userList = ['user1', 'user2'];
        $chatId = 'test_chat_id';
        
        // 设置模拟期望
        $this->workService->expects($this->once())
            ->method('request')
            ->willReturn(['chatid' => $chatId]);
            
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (AppChat $appChat) use ($chatId, $name, $owner, $userList) {
                return $appChat->getChatId() === $chatId
                    && $appChat->getName() === $name
                    && $appChat->getOwner() === $owner
                    && $appChat->getUserList() === $userList
                    && $appChat->isSynced() === true;
            }));
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $result = $this->appChatService->createAppChat($this->agent, $name, $owner, $userList);
        
        // 断言
        $this->assertInstanceOf(AppChat::class, $result);
        $this->assertEquals($chatId, $result->getChatId());
        $this->assertEquals($name, $result->getName());
        $this->assertEquals($owner, $result->getOwner());
        $this->assertEquals($userList, $result->getUserList());
        $this->assertTrue($result->isSynced());
        $this->assertNotNull($result->getLastSyncedAt());
    }

    public function testUpdateAppChat_withValidData(): void
    {
        // 设置模拟期望
        $this->workService->expects($this->once())
            ->method('request');
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $this->appChatService->updateAppChat($this->appChat);
        
        // 断言
        $this->assertTrue($this->appChat->isSynced());
        $this->assertNotNull($this->appChat->getLastSyncedAt());
    }

    public function testSyncAppChat_withValidResponse(): void
    {
        $response = [
            'name' => 'Updated Chat',
            'owner' => 'new_owner',
            'userlist' => ['user1', 'user2', 'user3'],
        ];
        
        // 设置模拟期望
        $this->workService->expects($this->once())
            ->method('request')
            ->willReturn($response);
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $this->appChatService->syncAppChat($this->appChat);
        
        // 断言
        $this->assertEquals($response['name'], $this->appChat->getName());
        $this->assertEquals($response['owner'], $this->appChat->getOwner());
        $this->assertEquals($response['userlist'], $this->appChat->getUserList());
        $this->assertTrue($this->appChat->isSynced());
        $this->assertNotNull($this->appChat->getLastSyncedAt());
    }

    public function testSyncAppChat_withException(): void
    {
        $exception = new \Exception('API Error');
        
        // 设置模拟期望
        $this->workService->expects($this->once())
            ->method('request')
            ->willThrowException($exception);
            
        $this->logger->expects($this->once())
            ->method('error')
            ->with('同步群聊信息失败', $this->anything());
            
        // 断言异常
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('API Error');
        
        // 执行测试
        $this->appChatService->syncAppChat($this->appChat);
    }

    public function testSyncUnsynced_withMultipleChats(): void
    {
        $secondAppChat = new AppChat();
        $secondAppChat->setAgent($this->agent);
        $secondAppChat->setChatId('test_chat_id_2');
        $secondAppChat->setName('Test Chat 2');
        $secondAppChat->setOwner('test_owner_2');
        $secondAppChat->setUserList(['user3', 'user4']);
        
        $unsynced = [$this->appChat, $secondAppChat];
        
        // 设置模拟期望
        $this->appChatRepository->expects($this->once())
            ->method('findUnsynced')
            ->willReturn($unsynced);
            
        $this->workService->expects($this->exactly(count($unsynced)))
            ->method('request')
            ->willReturn([
                'name' => 'Updated Chat',
                'owner' => 'new_owner',
                'userlist' => ['user1', 'user2', 'user3'],
            ]);
            
        $this->entityManager->expects($this->exactly(count($unsynced)))
            ->method('flush');
        
        // 执行测试
        $this->appChatService->syncUnsynced();
    }

    public function testSyncUnsynced_withException(): void
    {
        $unsynced = [$this->appChat];
        $exception = new \Exception('API Error');
        
        // 设置模拟期望
        $this->appChatRepository->expects($this->once())
            ->method('findUnsynced')
            ->willReturn($unsynced);
            
        $this->workService->expects($this->once())
            ->method('request')
            ->willThrowException($exception);
            
        $this->logger->expects($this->atLeastOnce())
            ->method('error')
            ->with('同步群聊信息失败', $this->anything());
        
        // 执行测试 - 不应抛出异常，因为异常已被捕获和记录
        $this->appChatService->syncUnsynced();
    }
} 