<?php

namespace WechatWorkAppChatBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use WechatWorkAppChatBundle\Repository\MarkdownMessageRepository;

#[ORM\Entity(repositoryClass: MarkdownMessageRepository::class)]
#[ORM\Table(name: 'wechat_work_app_chat_markdown_message', options: ['comment' => '企业微信群聊Markdown消息'])]
class MarkdownMessage extends BaseChatMessage
{
    #[ORM\Column(type: Types::TEXT, options: ['comment' => '消息内容'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 4096)]
    private string $content;

    public function getMsgType(): string
    {
        return 'markdown';
    }

    /**
     * @return array<string, mixed>
     */
    public function getRequestContent(): array
    {
        return [
            'markdown' => [
                'content' => $this->getContent(),
            ],
        ];
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function __toString(): string
    {
        return (string) $this->getId();
    }
}
