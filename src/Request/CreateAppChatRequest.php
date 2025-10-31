<?php

namespace WechatWorkAppChatBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

class CreateAppChatRequest extends ApiRequest
{
    use AgentAware;

    private string $name;

    private string $owner;

    /**
     * @var list<string>
     */
    private array $userList;

    public function getRequestPath(): string
    {
        return '/cgi-bin/appchat/create';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array
    {
        return [
            'json' => [
                'name' => $this->getName(),
                'owner' => $this->getOwner(),
                'userlist' => $this->getUserList(),
            ],
        ];
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
    public function getUserList(): array
    {
        return $this->userList;
    }

    /**
     * @param list<string> $userList
     */
    public function setUserList(array $userList): void
    {
        $this->userList = $userList;
    }
}
