<?php

namespace WechatWorkAppChatBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Entity\TextMessage;

#[When(env: 'test')]
#[When(env: 'dev')]
class TextMessageFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $textMessages = [
            [
                'content' => '这是一条测试文本消息',
                'isSent' => false,
            ],
            [
                'content' => '这是另一条已发送的文本消息',
                'isSent' => true,
                'sentAt' => new \DateTimeImmutable('2024-01-01 10:00:00'),
                'msgId' => 'msg_001',
            ],
            [
                'content' => '包含特殊字符的消息：@所有人 #测试 👍',
                'isSent' => false,
            ],
        ];

        foreach ($textMessages as $index => $data) {
            $textMessage = new TextMessage();
            $textMessage->setAppChat($this->getReference(AppChat::class . ($index % 2), AppChat::class));
            $textMessage->setContent($data['content']);
            $textMessage->setIsSent($data['isSent']);

            if (isset($data['sentAt'])) {
                $textMessage->setSentAt($data['sentAt']);
            }

            if (isset($data['msgId'])) {
                $textMessage->setMsgId($data['msgId']);
            }

            $this->addReference(TextMessage::class . $index, $textMessage);
            $manager->persist($textMessage);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [AppChatFixtures::class];
    }
}
