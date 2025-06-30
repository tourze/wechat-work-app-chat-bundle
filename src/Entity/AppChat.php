<?php

namespace WechatWorkAppChatBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkAppChatBundle\Repository\AppChatRepository;

#[ORM\Entity(repositoryClass: AppChatRepository::class)]
#[ORM\Table(name: 'wechat_work_app_chat_app_chat', options: ['comment' => '企业微信群聊会话'])]
class AppChat implements \Stringable
{
    use SnowflakeKeyAware;
    use TimestampableAware;
    use BlameableAware;

    #[ORM\ManyToOne(targetEntity: AgentInterface::class)]
    #[ORM\JoinColumn(nullable: false)]
    private AgentInterface $agent;

    #[ORM\Column(type: Types::STRING, length: 32, unique: true, options: ['comment' => '企业微信群聊ID'])]
    private string $chatId;

    #[ORM\Column(type: Types::STRING, length: 32, options: ['comment' => '群聊名称'])]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 32, options: ['comment' => '群主UserID'])]
    private string $owner;

    #[ORM\Column(type: Types::JSON, options: ['comment' => '群成员UserID列表'])]
    private array $userList = [];

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否同步到企业微信', 'default' => false])]
    private bool $isSynced = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '最后同步时间'])]
    private ?\DateTimeImmutable $lastSyncedAt = null;

    public function getAgent(): AgentInterface
    {
        return $this->agent;
    }

    public function setAgent(AgentInterface $agent): self
    {
        $this->agent = $agent;

        return $this;
    }

    public function getChatId(): string
    {
        return $this->chatId;
    }

    public function setChatId(string $chatId): self
    {
        $this->chatId = $chatId;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getOwner(): string
    {
        return $this->owner;
    }

    public function setOwner(string $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getUserList(): array
    {
        return $this->userList;
    }

    public function setUserList(array $userList): self
    {
        $this->userList = $userList;

        return $this;
    }

    public function isSynced(): bool
    {
        return $this->isSynced;
    }

    public function setIsSynced(bool $isSynced): self
    {
        $this->isSynced = $isSynced;

        return $this;
    }

    public function getLastSyncedAt(): ?\DateTimeImmutable
    {
        return $this->lastSyncedAt;
    }

    public function setLastSyncedAt(?\DateTimeImmutable $lastSyncedAt): self
    {
        $this->lastSyncedAt = $lastSyncedAt;

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->getId();
    }
}
