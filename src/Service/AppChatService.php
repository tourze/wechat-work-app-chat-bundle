<?php

namespace WechatWorkAppChatBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Repository\AppChatRepository;
use WechatWorkAppChatBundle\Request\CreateAppChatRequest;
use WechatWorkAppChatBundle\Request\GetAppChatRequest;
use WechatWorkAppChatBundle\Request\UpdateAppChatRequest;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Service\WorkService;

class AppChatService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AppChatRepository $appChatRepository,
        private readonly WorkService $workService,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function createAppChat(Agent $agent, string $name, string $owner, array $userList): AppChat
    {
        // 调用企业微信API创建群聊
        $request = new CreateAppChatRequest();
        $request->setAgent($agent);
        $request->setName($name);
        $request->setOwner($owner);
        $request->setUserList($userList);
        $response = $this->workService->request($request);

        $appChat = new AppChat();
        $appChat->setAgent($agent);
        $appChat->setChatId($response['chatid']);
        $appChat->setName($name);
        $appChat->setOwner($owner);
        $appChat->setUserList($userList);
        $appChat->setIsSynced(true);
        $appChat->setLastSyncedAt(new \DateTimeImmutable());

        $this->entityManager->persist($appChat);
        $this->entityManager->flush();

        return $appChat;
    }

    public function updateAppChat(AppChat $appChat): void
    {
        // 调用企业微信API更新群聊
        $request = new UpdateAppChatRequest();
        $request->setAgent($appChat->getAgent());
        $request->setChatId($appChat->getChatId());
        $request->setName($appChat->getName());
        $request->setOwner($appChat->getOwner());
        $this->workService->request($request);

        $appChat->setIsSynced(true);
        $appChat->setLastSyncedAt(new \DateTimeImmutable());

        $this->entityManager->flush();
    }

    public function syncAppChat(AppChat $appChat): void
    {
        try {
            // 调用企业微信API获取群聊信息
            $request = new GetAppChatRequest();
            $request->setAgent($appChat->getAgent());
            $request->setChatId($appChat->getChatId());
            $response = $this->workService->request($request);

            $appChat->setName($response['name']);
            $appChat->setOwner($response['owner']);
            $appChat->setUserList($response['userlist']);
            $appChat->setIsSynced(true);
            $appChat->setLastSyncedAt(new \DateTimeImmutable());

            $this->entityManager->flush();
        } catch (\Exception $e) {
            $this->logger->error('同步群聊信息失败', [
                'chat_id' => $appChat->getChatId(),
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function syncUnsynced(): void
    {
        $unsynced = $this->appChatRepository->findUnsynced();
        foreach ($unsynced as $appChat) {
            try {
                $this->syncAppChat($appChat);
            } catch (\Exception $e) {
                $this->logger->error('同步群聊信息失败', [
                    'chat_id' => $appChat->getChatId(),
                    'error' => $e->getMessage(),
                ]);
                continue;
            }
        }
    }
}
