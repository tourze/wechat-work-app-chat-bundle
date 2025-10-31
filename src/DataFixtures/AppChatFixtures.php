<?php

namespace WechatWorkAppChatBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;

#[When(env: 'test')]
#[When(env: 'dev')]
class AppChatFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建测试用的 Corp
        $testCorp = new Corp();
        $testCorp->setName('Test Corp');
        $testCorp->setCorpId('ww123456789abcdef');
        $testCorp->setCorpSecret('test_corp_secret');
        $manager->persist($testCorp);

        // 创建测试用的 Agent
        $testAgent = new Agent();
        $testAgent->setName('Test Chat Agent');
        $testAgent->setAgentId('test_agent_001');
        $testAgent->setSecret('test_agent_secret');
        $testAgent->setCorp($testCorp);
        $manager->persist($testAgent);

        $appChats = [
            [
                'chatId' => 'wrAAAAAAAAA-BBBBBBBBBBBBB',
                'name' => '测试群聊1',
                'owner' => 'user001',
                'userList' => ['user001', 'user002', 'user003'],
                'isSynced' => false,
            ],
            [
                'chatId' => 'wrCCCCCCCCCC-DDDDDDDDDDDDD',
                'name' => '测试群聊2',
                'owner' => 'user002',
                'userList' => ['user002', 'user004', 'user005'],
                'isSynced' => true,
                'lastSyncedAt' => new \DateTimeImmutable('2024-01-01 10:00:00'),
            ],
        ];

        foreach ($appChats as $index => $data) {
            $appChat = new AppChat();
            $appChat->setAgent($testAgent);
            $appChat->setChatId($data['chatId']);
            $appChat->setName($data['name']);
            $appChat->setOwner($data['owner']);
            $appChat->setUserList($data['userList']);
            $appChat->setIsSynced($data['isSynced']);

            if (isset($data['lastSyncedAt'])) {
                $appChat->setLastSyncedAt($data['lastSyncedAt']);
            }

            $this->addReference(AppChat::class . $index, $appChat);
            $manager->persist($appChat);
        }

        $manager->flush();
    }

    /**
     * @return array<class-string>
     */
    public function getDependencies(): array
    {
        return [];
    }
}
