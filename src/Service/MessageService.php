<?php

namespace WechatWorkAppChatBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Entity\FileMessage;
use WechatWorkAppChatBundle\Entity\ImageMessage;
use WechatWorkAppChatBundle\Entity\MarkdownMessage;
use WechatWorkAppChatBundle\Entity\TextMessage;
use WechatWorkAppChatBundle\Repository\FileMessageRepository;
use WechatWorkAppChatBundle\Repository\ImageMessageRepository;
use WechatWorkAppChatBundle\Repository\MarkdownMessageRepository;
use WechatWorkAppChatBundle\Repository\TextMessageRepository;
use WechatWorkAppChatBundle\Request\SendAppChatMessageRequest;
use WechatWorkBundle\Service\WorkService;

class MessageService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TextMessageRepository $textMessageRepository,
        private readonly MarkdownMessageRepository $markdownMessageRepository,
        private readonly ImageMessageRepository $imageMessageRepository,
        private readonly FileMessageRepository $fileMessageRepository,
        private readonly WorkService $workService,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function sendText(AppChat $appChat, string $content): TextMessage
    {
        $message = new TextMessage();
        $message->setAppChat($appChat);
        $message->setContent($content);

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return $message;
    }

    public function sendMarkdown(AppChat $appChat, string $content): MarkdownMessage
    {
        $message = new MarkdownMessage();
        $message->setAppChat($appChat);
        $message->setContent($content);

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return $message;
    }

    public function sendImage(AppChat $appChat, string $mediaId): ImageMessage
    {
        $message = new ImageMessage();
        $message->setAppChat($appChat);
        $message->setMediaId($mediaId);

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return $message;
    }

    public function sendFile(AppChat $appChat, string $mediaId): FileMessage
    {
        $message = new FileMessage();
        $message->setAppChat($appChat);
        $message->setMediaId($mediaId);

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return $message;
    }

    /**
     * 发送所有未发送的消息
     */
    public function sendUnsent(): void
    {
        $messages = array_merge(
            $this->textMessageRepository->findUnsent(),
            $this->markdownMessageRepository->findUnsent(),
            $this->imageMessageRepository->findUnsent(),
            $this->fileMessageRepository->findUnsent(),
        );

        foreach ($messages as $message) {
            /* @var MarkdownMessage $message */

            try {
                $request = new SendAppChatMessageRequest();
                $request->setMessage($message);
                $response = $this->workService->request($request);

                if (isset($response['msgid'])) {
                    $message->setMsgId($response['msgid']);
                    $message->setIsSent(true);
                    $message->setSentAt(new \DateTimeImmutable());
                    $this->entityManager->flush();
                }
            } catch  (\Throwable $e) {
                $this->logger->error('发送群聊消息失败', [
                    'chat_id' => $message->getAppChat()->getChatId(),
                    'error' => $e->getMessage(),
                ]);
                continue;
            }
        }
    }
}
