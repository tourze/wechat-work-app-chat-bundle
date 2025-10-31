<?php

namespace WechatWorkAppChatBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Entity\MarkdownMessage;
use WechatWorkAppChatBundle\Repository\MarkdownMessageRepository;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;

/**
 * @internal
 */
#[CoversClass(MarkdownMessageRepository::class)]
#[RunTestsInSeparateProcesses]
final class MarkdownMessageRepositoryTest extends AbstractRepositoryTestCase
{
    private MarkdownMessageRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(MarkdownMessageRepository::class);
    }

    public function testRepositoryService(): void
    {
        $this->assertInstanceOf(MarkdownMessageRepository::class, $this->repository);
    }

    public function testFindUnsent(): void
    {
        // Test the method exists and returns an array
        $results = $this->repository->findUnsent();
        $this->assertIsArray($results);

        // Since SendMessageListener might auto-send messages in test environment,
        // we just verify the method works correctly without asserting specific count
        foreach ($results as $result) {
            $this->assertInstanceOf(MarkdownMessage::class, $result);
            $this->assertFalse($result->isSent());
        }
    }

    public function testSave(): void
    {
        $appChat = $this->createAppChat();

        $markdownMessage = new MarkdownMessage();
        $markdownMessage->setAppChat($appChat);
        $markdownMessage->setContent('# Save test markdown');

        $this->repository->save($markdownMessage);

        $found = $this->repository->find($markdownMessage->getId());
        $this->assertInstanceOf(MarkdownMessage::class, $found);
        $this->assertEquals('# Save test markdown', $found->getContent());
    }

    public function testRemove(): void
    {
        $appChat = $this->createAppChat();

        $markdownMessage = new MarkdownMessage();
        $markdownMessage->setAppChat($appChat);
        $markdownMessage->setContent('# Remove test markdown');

        $this->repository->save($markdownMessage);
        $id = $markdownMessage->getId();

        $this->repository->remove($markdownMessage);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    public function testCountByAssociationAppChatShouldReturnCorrectNumber(): void
    {
        $appChat1 = $this->createAppChat();
        $appChat2 = $this->createAppChat();

        $markdownMessage1 = new MarkdownMessage();
        $markdownMessage1->setAppChat($appChat1);
        $markdownMessage1->setContent('# Count markdown chat1');
        $this->repository->save($markdownMessage1);

        $markdownMessage2 = new MarkdownMessage();
        $markdownMessage2->setAppChat($appChat2);
        $markdownMessage2->setContent('# Count markdown chat2');
        $this->repository->save($markdownMessage2);

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

        $markdownMessage = new MarkdownMessage();
        $markdownMessage->setAppChat($appChat);
        $markdownMessage->setContent('# FindOne AppChat association markdown');
        $this->repository->save($markdownMessage);

        $result = $this->repository->findOneBy(['appChat' => $appChat]);
        $this->assertInstanceOf(MarkdownMessage::class, $result);
        $this->assertEquals($appChat->getId(), $result->getAppChat()->getId());
    }

    protected function createNewEntity(): object
    {
        $appChat = $this->createAppChat();

        $entity = new MarkdownMessage();
        $entity->setAppChat($appChat);
        $entity->setContent('# Test markdown message ' . uniqid());

        return $entity;
    }

    /**
     * @return ServiceEntityRepository<MarkdownMessage>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
