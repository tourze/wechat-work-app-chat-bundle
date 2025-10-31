<?php

namespace WechatWorkAppChatBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Repository\AppChatRepository;
use WechatWorkAppChatBundle\Request\CreateAppChatRequest;
use WechatWorkAppChatBundle\Request\GetAppChatRequest;
use WechatWorkAppChatBundle\Request\UpdateAppChatRequest;
use WechatWorkBundle\Service\WorkServiceInterface;

#[Autoconfigure(public: true)]
#[WithMonologChannel(channel: 'wechat_work_app_chat')]
class AppChatService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AppChatRepository $appChatRepository,
        private readonly WorkServiceInterface $workService,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @param list<string> $userList
     */
    public function createAppChat(AgentInterface $agent, string $name, string $owner, array $userList): AppChat
    {
        // 调用企业微信API创建群聊
        $request = new CreateAppChatRequest();
        $request->setAgent($agent);
        $request->setName($name);
        $request->setOwner($owner);
        $request->setUserList($userList);
        /** @var array<string, mixed>|null $response */
        $response = $this->workService->request($request);

        $appChat = new AppChat();
        $appChat->setAgent($agent);
        if (is_array($response) && isset($response['chatid']) && is_string($response['chatid'])) {
            $appChat->setChatId($response['chatid']);
        }
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
            /** @var array<string, mixed>|null $response */
            $response = $this->workService->request($request);

            if (is_array($response)) {
                if (isset($response['name']) && is_string($response['name'])) {
                    $appChat->setName($response['name']);
                }
                if (isset($response['owner']) && is_string($response['owner'])) {
                    $appChat->setOwner($response['owner']);
                }
                if (isset($response['userlist']) && is_array($response['userlist'])) {
                    /** @var list<string> $userList */
                    $userList = array_values($response['userlist']);
                    $appChat->setUserList($userList);
                }
            }
            $appChat->setIsSynced(true);
            $appChat->setLastSyncedAt(new \DateTimeImmutable());

            $this->entityManager->flush();
        } catch (\Throwable $e) {
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
            } catch (\Throwable $e) {
                $this->logger->error('同步群聊信息失败', [
                    'chat_id' => $appChat->getChatId(),
                    'error' => $e->getMessage(),
                ]);
                continue;
            }
        }
    }
}
