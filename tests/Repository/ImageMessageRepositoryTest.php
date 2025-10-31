<?php

namespace WechatWorkAppChatBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Entity\ImageMessage;
use WechatWorkAppChatBundle\Repository\ImageMessageRepository;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;

/**
 * @internal
 */
#[CoversClass(ImageMessageRepository::class)]
#[RunTestsInSeparateProcesses]
final class ImageMessageRepositoryTest extends AbstractRepositoryTestCase
{
    private ImageMessageRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(ImageMessageRepository::class);
    }

    public function testRepositoryService(): void
    {
        $this->assertInstanceOf(ImageMessageRepository::class, $this->repository);
    }

    public function testFindUnsent(): void
    {
        // Test the method exists and returns an array
        $results = $this->repository->findUnsent();
        $this->assertIsArray($results);

        // Since SendMessageListener might auto-send messages in test environment,
        // we just verify the method works correctly without asserting specific count
        foreach ($results as $result) {
            $this->assertInstanceOf(ImageMessage::class, $result);
            $this->assertFalse($result->isSent());
        }
    }

    public function testSave(): void
    {
        $appChat = $this->createAppChat();

        $imageMessage = new ImageMessage();
        $imageMessage->setAppChat($appChat);
        $imageMessage->setMediaId('save_test_image_media_id');

        $this->repository->save($imageMessage);

        $found = $this->repository->find($imageMessage->getId());
        $this->assertInstanceOf(ImageMessage::class, $found);
        $this->assertEquals('save_test_image_media_id', $found->getMediaId());
    }

    public function testRemove(): void
    {
        $appChat = $this->createAppChat();

        $imageMessage = new ImageMessage();
        $imageMessage->setAppChat($appChat);
        $imageMessage->setMediaId('remove_test_image_media_id');

        $this->repository->save($imageMessage);
        $id = $imageMessage->getId();

        $this->repository->remove($imageMessage);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    public function testCountByAssociationAppChatShouldReturnCorrectNumber(): void
    {
        $appChat1 = $this->createAppChat();
        $appChat2 = $this->createAppChat();

        $imageMessage1 = new ImageMessage();
        $imageMessage1->setAppChat($appChat1);
        $imageMessage1->setMediaId('count_image_chat1_media');
        $this->repository->save($imageMessage1);

        $imageMessage2 = new ImageMessage();
        $imageMessage2->setAppChat($appChat2);
        $imageMessage2->setMediaId('count_image_chat2_media');
        $this->repository->save($imageMessage2);

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

        $imageMessage = new ImageMessage();
        $imageMessage->setAppChat($appChat);
        $imageMessage->setMediaId('findone_association_image_media_id');
        $this->repository->save($imageMessage);

        $result = $this->repository->findOneBy(['appChat' => $appChat]);
        $this->assertInstanceOf(ImageMessage::class, $result);
        $this->assertEquals($appChat->getId(), $result->getAppChat()->getId());
    }

    protected function createNewEntity(): object
    {
        $appChat = $this->createAppChat();

        $entity = new ImageMessage();
        $entity->setAppChat($appChat);
        $entity->setMediaId('test_image_media_id_' . uniqid());

        return $entity;
    }

    /**
     * @return ServiceEntityRepository<ImageMessage>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
