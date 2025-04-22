<?php

namespace WechatWorkAppChatBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

class UpdateAppChatRequest extends ApiRequest
{
    use AgentAware;

    private string $chatId;

    private string $name;

    private string $owner;

    private array $addUserList = [];

    private array $delUserList = [];

    public function getRequestPath(): string
    {
        return '/cgi-bin/appchat/update';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'json' => [
                'chatid' => $this->getChatId(),
                'name' => $this->getName(),
                'owner' => $this->getOwner(),
                'add_user_list' => $this->getAddUserList(),
                'del_user_list' => $this->getDelUserList(),
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getOwner(): string
    {
        return $this->owner;
    }

    public function setOwner(string $owner): void
    {
        $this->owner = $owner;
    }

    public function getAddUserList(): array
    {
        return $this->addUserList;
    }

    public function setAddUserList(array $addUserList): void
    {
        $this->addUserList = $addUserList;
    }

    public function getDelUserList(): array
    {
        return $this->delUserList;
    }

    public function setDelUserList(array $delUserList): void
    {
        $this->delUserList = $delUserList;
    }
}
