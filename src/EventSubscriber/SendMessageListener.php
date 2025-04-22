<?php

namespace WechatWorkAppChatBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;
use WechatWorkAppChatBundle\Entity\BaseChatMessage;
use WechatWorkAppChatBundle\Entity\FileMessage;
use WechatWorkAppChatBundle\Entity\ImageMessage;
use WechatWorkAppChatBundle\Entity\MarkdownMessage;
use WechatWorkAppChatBundle\Entity\TextMessage;
use WechatWorkAppChatBundle\Request\SendAppChatMessageRequest;
use WechatWorkBundle\Service\WorkService;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: TextMessage::class)]
#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: MarkdownMessage::class)]
#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: ImageMessage::class)]
#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: FileMessage::class)]
class SendMessageListener
{
    public function __construct(
        private readonly WorkService $workService,
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
            $response = $this->workService->request($request);

            if (isset($response['msgid'])) {
                $message->setMsgId($response['msgid']);
                $message->setIsSent(true);
                $message->setSentAt(new \DateTimeImmutable());
                $args->getObjectManager()->flush();
            }
        } catch (\Exception $e) {
            $this->logger->error('自动发送群聊消息失败', [
                'chat_id' => $message->getAppChat()->getChatId(),
                'error' => $e->getMessage(),
            ]);
        }
    }
}
