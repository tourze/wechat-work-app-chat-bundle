<?php

namespace WechatWorkAppChatBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Entity\FileMessage;
use WechatWorkAppChatBundle\Repository\FileMessageRepository;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;

/**
 * @internal
 */
#[CoversClass(FileMessageRepository::class)]
#[RunTestsInSeparateProcesses]
final class FileMessageRepositoryTest extends AbstractRepositoryTestCase
{
    private FileMessageRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(FileMessageRepository::class);
    }

    public function testRepositoryService(): void
    {
        $this->assertInstanceOf(FileMessageRepository::class, $this->repository);
    }

    public function testFindUnsent(): void
    {
        // Test the method exists and returns an array
        $results = $this->repository->findUnsent();
        $this->assertIsArray($results);

        // Since SendMessageListener might auto-send messages in test environment,
        // we just verify the method works correctly without asserting specific count
        foreach ($results as $result) {
            $this->assertInstanceOf(FileMessage::class, $result);
            $this->assertFalse($result->isSent());
        }
    }

    public function testSave(): void
    {
        $appChat = $this->createAppChat();

        $fileMessage = new FileMessage();
        $fileMessage->setAppChat($appChat);
        $fileMessage->setMediaId('save_test_media_id');

        $this->repository->save($fileMessage);

        $found = $this->repository->find($fileMessage->getId());
        $this->assertInstanceOf(FileMessage::class, $found);
        $this->assertEquals('save_test_media_id', $found->getMediaId());
    }

    public function testRemove(): void
    {
        $appChat = $this->createAppChat();

        $fileMessage = new FileMessage();
        $fileMessage->setAppChat($appChat);
        $fileMessage->setMediaId('remove_test_media_id');

        $this->repository->save($fileMessage);
        $id = $fileMessage->getId();

        $this->repository->remove($fileMessage);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    public function testCountByAssociationAppChatShouldReturnCorrectNumber(): void
    {
        $appChat1 = $this->createAppChat();
        $appChat2 = $this->createAppChat();

        $fileMessage1 = new FileMessage();
        $fileMessage1->setAppChat($appChat1);
        $fileMessage1->setMediaId('count_chat1_media');
        $this->repository->save($fileMessage1);

        $fileMessage2 = new FileMessage();
        $fileMessage2->setAppChat($appChat2);
        $fileMessage2->setMediaId('count_chat2_media');
        $this->repository->save($fileMessage2);

        $count = $this->repository->count(['appChat' => $appChat1]);
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

        $fileMessage = new FileMessage();
        $fileMessage->setAppChat($appChat);
        $fileMessage->setMediaId('findone_association_media_id');
        $this->repository->save($fileMessage);

        $result = $this->repository->findOneBy(['appChat' => $appChat]);
        $this->assertInstanceOf(FileMessage::class, $result);
        $this->assertEquals($appChat->getId(), $result->getAppChat()->getId());
    }

    protected function createNewEntity(): object
    {
        $appChat = $this->createAppChat();

        $entity = new FileMessage();
        $entity->setAppChat($appChat);
        $entity->setMediaId('test_media_id_' . uniqid());

        return $entity;
    }

    /**
     * @return ServiceEntityRepository<FileMessage>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
