<?php

declare(strict_types=1);

namespace WechatWorkAppChatBundle\Tests\Service;

use HttpClientBundle\Request\RequestInterface;
use WechatWorkAppChatBundle\Request\CreateAppChatRequest;
use WechatWorkAppChatBundle\Request\GetAppChatRequest;
use WechatWorkAppChatBundle\Request\SendAppChatMessageRequest;
use WechatWorkAppChatBundle\Request\UpdateAppChatRequest;
use WechatWorkBundle\Service\WorkServiceInterface;

/**
 * 模拟的 WorkService 用于测试
 * 实现 WorkServiceInterface 接口，确保类型安全的依赖注入
 */
class MockWorkService implements WorkServiceInterface
{
    public function __construct()
    {
        // 无需任何依赖，是一个纯粹的Mock对象
    }

    /**
     * 模拟请求方法，返回预设的响应数据
     */
    public function request(RequestInterface $request): mixed
    {
        if ($request instanceof SendAppChatMessageRequest) {
            return [
                'msgid' => 'mock_msg_id_' . uniqid(),
                'errcode' => 0,
                'errmsg' => 'ok',
            ];
        }

        if ($request instanceof CreateAppChatRequest) {
            return [
                'chatid' => 'mock_chat_id_' . uniqid(),
                'errcode' => 0,
                'errmsg' => 'ok',
            ];
        }

        if ($request instanceof UpdateAppChatRequest) {
            return [
                'errcode' => 0,
                'errmsg' => 'ok',
            ];
        }

        if ($request instanceof GetAppChatRequest) {
            return [
                'name' => 'Mock Chat',
                'owner' => 'mock_owner',
                'userlist' => ['user1', 'user2'],
                'errcode' => 0,
                'errmsg' => 'ok',
            ];
        }

        return [
            'errcode' => 0,
            'errmsg' => 'ok',
        ];
    }

    /**
     * 模拟基础URL获取
     */
    public function getBaseUrl(): string
    {
        return 'https://mock.qyapi.weixin.qq.com';
    }

    /**
     * 模拟访问令牌刷新（空实现）
     */
    public function refreshAgentAccessToken(mixed $agent): void
    {
        // Mock实现，无需实际操作
    }
}
