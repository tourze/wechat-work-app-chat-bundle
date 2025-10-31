<?php

namespace WechatWorkAppChatBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Repository\AppChatRepository;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;

/**
 * @internal
 */
#[CoversClass(AppChatRepository::class)]
#[RunTestsInSeparateProcesses]
final class AppChatRepositoryTest extends AbstractRepositoryTestCase
{
    private AppChatRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(AppChatRepository::class);
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

    public function testRepositoryService(): void
    {
        $this->assertInstanceOf(AppChatRepository::class, $this->repository);
    }

    public function testFindByChatId(): void
    {
        $agent = $this->createTestAgent();

        $appChat = new AppChat();
        $appChat->setAgent($agent);
        $appChat->setChatId('unique_chat_id');
        $appChat->setName('Unique Chat');
        $appChat->setOwner('unique_owner');
        $appChat->setUserList(['user1', 'user2']);

        $this->repository->save($appChat);

        $result = $this->repository->findByChatId('unique_chat_id');
        $this->assertInstanceOf(AppChat::class, $result);
        $this->assertEquals('unique_chat_id', $result->getChatId());
    }

    public function testFindUnsynced(): void
    {
        $agent = $this->createTestAgent();

        $appChatSynced = new AppChat();
        $appChatSynced->setAgent($agent);
        $appChatSynced->setChatId('synced_chat_id');
        $appChatSynced->setName('Synced Chat');
        $appChatSynced->setOwner('synced_owner');
        $appChatSynced->setUserList(['user1', 'user2']);
        $appChatSynced->setIsSynced(true);
        $this->repository->save($appChatSynced);

        $appChatUnsynced = new AppChat();
        $appChatUnsynced->setAgent($agent);
        $appChatUnsynced->setChatId('unsynced_chat_id');
        $appChatUnsynced->setName('Unsynced Chat');
        $appChatUnsynced->setOwner('unsynced_owner');
        $appChatUnsynced->setUserList(['user1', 'user2']);
        $appChatUnsynced->setIsSynced(false);
        $this->repository->save($appChatUnsynced);

        $results = $this->repository->findUnsynced();
        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(1, count($results));

        foreach ($results as $result) {
            $this->assertInstanceOf(AppChat::class, $result);
            $this->assertFalse($result->isSynced());
        }
    }

    public function testSave(): void
    {
        $agent = $this->createTestAgent();

        $appChat = new AppChat();
        $appChat->setAgent($agent);
        $appChat->setChatId('save_test_chat_id');
        $appChat->setName('Save Test Chat');
        $appChat->setOwner('save_test_owner');
        $appChat->setUserList(['user1', 'user2']);

        $this->repository->save($appChat);

        $found = $this->repository->find($appChat->getId());
        $this->assertInstanceOf(AppChat::class, $found);
        $this->assertEquals('save_test_chat_id', $found->getChatId());
    }

    public function testRemove(): void
    {
        $agent = $this->createTestAgent();

        $appChat = new AppChat();
        $appChat->setAgent($agent);
        $appChat->setChatId('remove_test_chat_id');
        $appChat->setName('Remove Test Chat');
        $appChat->setOwner('remove_test_owner');
        $appChat->setUserList(['user1', 'user2']);

        $this->repository->save($appChat);
        $id = $appChat->getId();

        $this->repository->remove($appChat);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    public function testCountByAssociationAgentShouldReturnCorrectNumber(): void
    {
        $agent1 = $this->createTestAgent();
        $agent2 = $this->createTestAgent();

        $appChat1 = new AppChat();
        $appChat1->setAgent($agent1);
        $appChat1->setChatId('count_agent1_chat');
        $appChat1->setName('Agent1 Chat');
        $appChat1->setOwner('owner1');
        $appChat1->setUserList(['user1']);
        $this->repository->save($appChat1);

        $appChat2 = new AppChat();
        $appChat2->setAgent($agent2);
        $appChat2->setChatId('count_agent2_chat');
        $appChat2->setName('Agent2 Chat');
        $appChat2->setOwner('owner2');
        $appChat2->setUserList(['user2']);
        $this->repository->save($appChat2);

        $count = $this->repository->count(['agent' => $agent1]);
        $this->assertIsInt($count);
        $this->assertEquals(1, $count);
    }

    public function testFindOneByAssociationAgentShouldReturnMatchingEntity(): void
    {
        $agent = $this->createTestAgent();

        $appChat = new AppChat();
        $appChat->setAgent($agent);
        $appChat->setChatId('findone_agent_association_id');
        $appChat->setName('FindOne Agent Association Chat');
        $appChat->setOwner('findone_agent_owner');
        $appChat->setUserList(['user1', 'user2']);

        $this->repository->save($appChat);

        $result = $this->repository->findOneBy(['agent' => $agent]);
        $this->assertInstanceOf(AppChat::class, $result);
        $this->assertEquals($agent->getAgentId(), $result->getAgent()->getAgentId());
    }

    protected function createNewEntity(): object
    {
        $agent = $this->createTestAgent();

        $entity = new AppChat();
        $entity->setAgent($agent);
        $entity->setChatId('test_chat_' . uniqid());
        $entity->setName('Test AppChat ' . uniqid());
        $entity->setOwner('test_owner');
        $entity->setUserList(['user1', 'user2']);

        return $entity;
    }

    /**
     * @return ServiceEntityRepository<AppChat>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
