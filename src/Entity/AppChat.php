<?php

namespace WechatWorkAppChatBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkAppChatBundle\Repository\AppChatRepository;

#[ORM\Entity(repositoryClass: AppChatRepository::class)]
#[ORM\Table(name: 'wechat_work_app_chat_app_chat', options: ['comment' => '企业微信群聊会话'])]
class AppChat implements \Stringable
{
    use TimestampableAware;
    use BlameableAware;
    use SnowflakeKeyAware;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    #[Assert\Length(max: 20)]
    protected ?string $id = null;

    #[ORM\ManyToOne(targetEntity: 'WechatWorkBundle\Entity\Agent', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private AgentInterface $agent;

    #[ORM\Column(type: Types::STRING, length: 32, unique: true, options: ['comment' => '企业微信群聊ID'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    private string $chatId;

    #[ORM\Column(type: Types::STRING, length: 32, options: ['comment' => '群聊名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 32, options: ['comment' => '群主UserID'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    private string $owner;

    /**
     * @var list<string>
     */
    #[ORM\Column(type: Types::JSON, options: ['comment' => '群成员UserID列表'])]
    #[Assert\Type(type: 'array')]
    private array $userList = [];

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否同步到企业微信', 'default' => false])]
    #[Assert\Type(type: 'bool')]
    private bool $isSynced = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '最后同步时间'])]
    #[Assert\Type(type: '\DateTimeImmutable')]
    private ?\DateTimeImmutable $lastSyncedAt = null;

    public function getAgent(): AgentInterface
    {
        return $this->agent;
    }

    public function setAgent(AgentInterface $agent): void
    {
        $this->agent = $agent;
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

    public function isSynced(): bool
    {
        return $this->isSynced;
    }

    public function setIsSynced(bool $isSynced): void
    {
        $this->isSynced = $isSynced;
    }

    public function getLastSyncedAt(): ?\DateTimeImmutable
    {
        return $this->lastSyncedAt;
    }

    public function setLastSyncedAt(?\DateTimeImmutable $lastSyncedAt): void
    {
        $this->lastSyncedAt = $lastSyncedAt;
    }

    public function __toString(): string
    {
        return (string) $this->getId();
    }
}
