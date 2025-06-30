<?php

namespace WechatWorkAppChatBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\MappedSuperclass]
abstract class BaseChatMessage
{
    use SnowflakeKeyAware;
    use TimestampableAware;
    use BlameableAware;

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

    abstract public function getRequestContent(): array;

    public function getAppChat(): AppChat
    {
        return $this->appChat;
    }

    public function setAppChat(AppChat $appChat): self
    {
        $this->appChat = $appChat;

        return $this;
    }

    public function isSent(): bool
    {
        return $this->isSent;
    }

    public function setIsSent(bool $isSent): self
    {
        $this->isSent = $isSent;

        return $this;
    }

    public function getSentAt(): ?\DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function setSentAt(?\DateTimeImmutable $sentAt): self
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getMsgId(): ?string
    {
        return $this->msgId;
    }

    public function setMsgId(?string $msgId): self
    {
        $this->msgId = $msgId;

        return $this;
    }

    public function isRecalled(): bool
    {
        return $this->isRecalled;
    }

    public function setIsRecalled(bool $isRecalled): self
    {
        $this->isRecalled = $isRecalled;

        return $this;
    }

    public function getRecalledAt(): ?\DateTimeImmutable
    {
        return $this->recalledAt;
    }

    public function setRecalledAt(?\DateTimeImmutable $recalledAt): self
    {
        $this->recalledAt = $recalledAt;

        return $this;
    }
}
