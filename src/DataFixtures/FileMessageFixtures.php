<?php

namespace WechatWorkAppChatBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Entity\FileMessage;

#[When(env: 'test')]
#[When(env: 'dev')]
class FileMessageFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $fileMessages = [
            [
                'mediaId' => 'media_file_001',
                'isSent' => false,
            ],
            [
                'mediaId' => 'media_file_002',
                'isSent' => true,
                'sentAt' => new \DateTimeImmutable('2024-01-01 13:00:00'),
                'msgId' => 'msg_004',
            ],
            [
                'mediaId' => '123456789012345678901234567890',
                'isSent' => false,
            ],
        ];

        foreach ($fileMessages as $index => $data) {
            $fileMessage = new FileMessage();
            $fileMessage->setAppChat($this->getReference(AppChat::class . ($index % 2), AppChat::class));
            $fileMessage->setMediaId($data['mediaId']);
            $fileMessage->setIsSent($data['isSent']);

            if (isset($data['sentAt'])) {
                $fileMessage->setSentAt($data['sentAt']);
            }

            if (isset($data['msgId'])) {
                $fileMessage->setMsgId($data['msgId']);
            }

            $this->addReference(FileMessage::class . $index, $fileMessage);
            $manager->persist($fileMessage);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [AppChatFixtures::class];
    }
}
