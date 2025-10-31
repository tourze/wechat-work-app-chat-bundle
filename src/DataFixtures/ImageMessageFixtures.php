<?php

namespace WechatWorkAppChatBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Entity\ImageMessage;

#[When(env: 'test')]
#[When(env: 'dev')]
class ImageMessageFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $imageMessages = [
            [
                'mediaId' => 'media_image_001',
                'isSent' => false,
            ],
            [
                'mediaId' => 'media_image_002',
                'isSent' => true,
                'sentAt' => new \DateTimeImmutable('2024-01-01 12:00:00'),
                'msgId' => 'msg_003',
            ],
            [
                'mediaId' => 'media_image_003_with_long_id_string',
                'isSent' => false,
            ],
        ];

        foreach ($imageMessages as $index => $data) {
            $imageMessage = new ImageMessage();
            $imageMessage->setAppChat($this->getReference(AppChat::class . ($index % 2), AppChat::class));
            $imageMessage->setMediaId($data['mediaId']);
            $imageMessage->setIsSent($data['isSent']);

            if (isset($data['sentAt'])) {
                $imageMessage->setSentAt($data['sentAt']);
            }

            if (isset($data['msgId'])) {
                $imageMessage->setMsgId($data['msgId']);
            }

            $this->addReference(ImageMessage::class . $index, $imageMessage);
            $manager->persist($imageMessage);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [AppChatFixtures::class];
    }
}
