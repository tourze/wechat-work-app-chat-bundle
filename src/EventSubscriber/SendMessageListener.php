<?php

namespace WechatWorkAppChatBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use WechatWorkAppChatBundle\Entity\BaseChatMessage;
use WechatWorkAppChatBundle\Entity\FileMessage;
use WechatWorkAppChatBundle\Entity\ImageMessage;
use WechatWorkAppChatBundle\Entity\MarkdownMessage;
use WechatWorkAppChatBundle\Entity\TextMessage;
use WechatWorkAppChatBundle\Request\SendAppChatMessageRequest;
use WechatWorkBundle\Service\WorkServiceInterface;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: TextMessage::class)]
#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: MarkdownMessage::class)]
#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: ImageMessage::class)]
#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: FileMessage::class)]
#[WithMonologChannel(channel: 'wechat_work_app_chat')]
class SendMessageListener
{
    public function __construct(
        private readonly WorkServiceInterface $workService,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @throws \Throwable
     */
    public function postPersist(BaseChatMessage $message, PostPersistEventArgs $args): void
    {
        try {
            $request = new SendAppChatMessageRequest();
            $request->setAgent($message->getAppChat()->getAgent());
            $request->setMessage($message);
            /** @var array<string, mixed>|null $response */
            $response = $this->workService->request($request);

            if (is_array($response) && isset($response['msgid']) && is_string($response['msgid'])) {
                $message->setMsgId($response['msgid']);
                $message->setIsSent(true);
                $message->setSentAt(new \DateTimeImmutable());
                $args->getObjectManager()->flush();
            }
        } catch (\Throwable $e) {
            $this->logger->error('自动发送群聊消息失败', [
                'chat_id' => $message->getAppChat()->getChatId(),
                'error' => $e->getMessage(),
            ]);
        }
    }
}
