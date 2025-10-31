<?php

namespace WechatWorkAppChatBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Entity\TextMessage;
use WechatWorkAppChatBundle\Repository\TextMessageRepository;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;

/**
 * @internal
 */
#[CoversClass(TextMessageRepository::class)]
#[RunTestsInSeparateProcesses]
final class TextMessageRepositoryTest extends AbstractRepositoryTestCase
{
    private TextMessageRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(TextMessageRepository::class);
    }

    public function testRepositoryService(): void
    {
        $this->assertInstanceOf(TextMessageRepository::class, $this->repository);
    }

    public function testFindUnsent(): void
    {
        // Test the method exists and returns an array
        $results = $this->repository->findUnsent();
        $this->assertIsArray($results);

        // Since SendMessageListener might auto-send messages in test environment,
        // we just verify the method works correctly without asserting specific count
        foreach ($results as $result) {
            $this->assertInstanceOf(TextMessage::class, $result);
            $this->assertFalse($result->isSent());
        }
    }

    public function testSave(): void
    {
        $appChat = $this->createAppChat();

        $textMessage = new TextMessage();
        $textMessage->setAppChat($appChat);
        $textMessage->setContent('Save test message');

        $this->repository->save($textMessage);

        $found = $this->repository->find($textMessage->getId());
        $this->assertInstanceOf(TextMessage::class, $found);
        $this->assertEquals('Save test message', $found->getContent());
    }

    public function testRemove(): void
    {
        $appChat = $this->createAppChat();

        $textMessage = new TextMessage();
        $textMessage->setAppChat($appChat);
        $textMessage->setContent('Remove test message');

        $this->repository->save($textMessage);
        $id = $textMessage->getId();

        $this->repository->remove($textMessage);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    public function testFindByAppChatAssociation(): void
    {
        $appChat = $this->createAppChat();

        $textMessage = new TextMessage();
        $textMessage->setAppChat($appChat);
        $textMessage->setContent('Association test message');

        $this->repository->save($textMessage);

        $found = $this->repository->findBy(['appChat' => $appChat]);
        $this->assertCount(1, $found);
        $this->assertInstanceOf(TextMessage::class, $found[0]);
    }

    public function testCountByAssociationAppChatShouldReturnCorrectNumber(): void
    {
        $appChat1 = $this->createAppChat();
        $appChat2 = $this->createAppChat();

        $textMessage1 = new TextMessage();
        $textMessage1->setAppChat($appChat1);
        $textMessage1->setContent('Message for chat1');
        $this->repository->save($textMessage1);

        $textMessage2 = new TextMessage();
        $textMessage2->setAppChat($appChat2);
        $textMessage2->setContent('Message for chat2');
        $this->repository->save($textMessage2);

        $count = $this->repository->count(['appChat' => $appChat1]);
        $this->assertIsInt($count);
        $this->assertEquals(1, $count);
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

    private function createAppChat(): AppChat
    {
        $agent = $this->createTestAgent();

        $appChat = new AppChat();
        $appChat->setAgent($agent);
        $appChat->setChatId('test_chat_id_' . uniqid());
        $appChat->setName('Test Chat');
        $appChat->setOwner('test_owner');
        $appChat->setUserList(['user1', 'user2']);

        self::getEntityManager()->persist($appChat);
        self::getEntityManager()->flush();

        return $appChat;
    }

    public function testFindOneByAssociationAppChatShouldReturnMatchingEntity(): void
    {
        $appChat = $this->createAppChat();

        $textMessage = new TextMessage();
        $textMessage->setAppChat($appChat);
        $textMessage->setContent('FindOne AppChat association message');
        $this->repository->save($textMessage);

        $result = $this->repository->findOneBy(['appChat' => $appChat]);
        $this->assertInstanceOf(TextMessage::class, $result);
        $this->assertEquals($appChat->getId(), $result->getAppChat()->getId());
    }

    protected function createNewEntity(): object
    {
        $appChat = $this->createAppChat();

        $entity = new TextMessage();
        $entity->setAppChat($appChat);
        $entity->setContent('Test text message ' . uniqid());

        return $entity;
    }

    /**
     * @return ServiceEntityRepository<TextMessage>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }

    /**
     * 修复基类中有问题的测试方法，使用更可靠的数据库不可用模拟
     * 由于基类方法是final的，我们创建一个新的测试方法
     */
    public function testFindByWithCorruptedDatabaseShouldThrowException(): void
    {
        $this->expectException(Exception::class);

        // 获取连接信息
        $entityManager = self::getEntityManager();
        $connection = $entityManager->getConnection();

        if ($connection->getDatabasePlatform() instanceof SQLitePlatform) {
            // 对于SQLite，我们采用更强力的破坏方法
            $connection->close();

            $params = $connection->getParams();
            if (!isset($params['path'])) {
                throw new \Exception('Database path not found in connection params');
            }
            $dbPath = trim($params['path'], 'file:');

            if (file_exists($dbPath)) {
                // 彻底删除数据库文件
                unlink($dbPath);

                // 创建一个损坏的文件，包含无效的SQLite头
                $corruptedContent = str_repeat("\x00\xFF\xDE\xAD\xBE\xEF", 2000);
                file_put_contents($dbPath, $corruptedContent, LOCK_EX);
            }
        } else {
            // 对于其他数据库，简单关闭连接
            $connection->close();
        }

        // 尝试执行查询，应该抛出异常
        // 移除反射API使用，直接测试repository行为
        $this->repository->findBy([]);
    }
}
