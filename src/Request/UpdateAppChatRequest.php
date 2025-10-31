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

    /**
     * @var list<string>
     */
    private array $addUserList = [];

    /**
     * @var list<string>
     */
    private array $delUserList = [];

    public function getRequestPath(): string
    {
        return '/cgi-bin/appchat/update';
    }

    /**
     * @return array<string, mixed>|null
     */
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

    /**
     * @return list<string>
     */
    public function getAddUserList(): array
    {
        return $this->addUserList;
    }

    /**
     * @param list<string> $addUserList
     */
    public function setAddUserList(array $addUserList): void
    {
        $this->addUserList = $addUserList;
    }

    /**
     * @return list<string>
     */
    public function getDelUserList(): array
    {
        return $this->delUserList;
    }

    /**
     * @param list<string> $delUserList
     */
    public function setDelUserList(array $delUserList): void
    {
        $this->delUserList = $delUserList;
    }
}
