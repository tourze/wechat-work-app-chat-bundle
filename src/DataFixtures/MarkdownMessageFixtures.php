<?php

namespace WechatWorkAppChatBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Entity\MarkdownMessage;

#[When(env: 'test')]
#[When(env: 'dev')]
class MarkdownMessageFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $markdownMessages = [
            [
                'content' => '# 测试标题\n\n这是一个**粗体**文本，还有*斜体*文本。',
                'isSent' => false,
            ],
            [
                'content' => '## 已发送消息\n\n- 列表项1\n- 列表项2\n\n[链接](https://www.tourze.cn)',
                'isSent' => true,
                'sentAt' => new \DateTimeImmutable('2024-01-01 11:00:00'),
                'msgId' => 'msg_002',
            ],
            [
                'content' => '```php\n<?php\necho "Hello World";\n```',
                'isSent' => false,
            ],
        ];

        foreach ($markdownMessages as $index => $data) {
            $markdownMessage = new MarkdownMessage();
            $markdownMessage->setAppChat($this->getReference(AppChat::class . ($index % 2), AppChat::class));
            $markdownMessage->setContent($data['content']);
            $markdownMessage->setIsSent($data['isSent']);

            if (isset($data['sentAt'])) {
                $markdownMessage->setSentAt($data['sentAt']);
            }

            if (isset($data['msgId'])) {
                $markdownMessage->setMsgId($data['msgId']);
            }

            $this->addReference(MarkdownMessage::class . $index, $markdownMessage);
            $manager->persist($markdownMessage);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [AppChatFixtures::class];
    }
}
