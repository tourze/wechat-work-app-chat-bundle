<?php

namespace WechatWorkAppChatBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\MappedSuperclass]
abstract class BaseChatMessage
{
    use BlameableAware;
    use SnowflakeKeyAware;
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    #[Assert\Length(max: 20)]
    protected ?string $id = null;

    #[ORM\ManyToOne(targetEntity: AppChat::class)]
    #[ORM\JoinColumn(nullable: false)]
    protected AppChat $appChat;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否已发送', 'default' => false])]
    protected bool $isSent = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '发送时间'])]
    protected ?\DateTimeImmutable $sentAt = null;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '消息ID'])]
    protected ?string $msgId = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否已撤回', 'default' => false])]
    protected bool $isRecalled = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '撤回时间'])]
    protected ?\DateTimeImmutable $recalledAt = null;

    abstract public function getMsgType(): string;

    /**
     * @return array<string, mixed>
     */
    abstract public function getRequestContent(): array;

    public function getAppChat(): AppChat
    {
        return $this->appChat;
    }

    public function setAppChat(AppChat $appChat): void
    {
        $this->appChat = $appChat;
    }

    public function isSent(): bool
    {
        return $this->isSent;
    }

    public function setIsSent(bool $isSent): void
    {
        $this->isSent = $isSent;
    }

    public function getSentAt(): ?\DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function setSentAt(?\DateTimeImmutable $sentAt): void
    {
        $this->sentAt = $sentAt;
    }

    public function getMsgId(): ?string
    {
        return $this->msgId;
    }

    public function setMsgId(?string $msgId): void
    {
        $this->msgId = $msgId;
    }

    public function isRecalled(): bool
    {
        return $this->isRecalled;
    }

    public function setIsRecalled(bool $isRecalled): void
    {
        $this->isRecalled = $isRecalled;
    }

    public function getRecalledAt(): ?\DateTimeImmutable
    {
        return $this->recalledAt;
    }

    public function setRecalledAt(?\DateTimeImmutable $recalledAt): void
    {
        $this->recalledAt = $recalledAt;
    }
}
