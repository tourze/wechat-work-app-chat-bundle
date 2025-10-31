<?php

namespace WechatWorkAppChatBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

class GetAppChatRequest extends ApiRequest
{
    use AgentAware;

    private string $chatId;

    public function getRequestPath(): string
    {
        return '/cgi-bin/appchat/get';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array
    {
        return [
            'query' => [
                'chatid' => $this->getChatId(),
            ],
        ];
    }

    public function getChatId(): string
    {
        return $this->chatId;
    }

    public function setChatId(string $chatId): void
    {
        $this->chatId = $chatId;
    }
}
