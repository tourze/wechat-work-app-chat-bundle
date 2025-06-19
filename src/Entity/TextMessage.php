<?php

namespace WechatWorkAppChatBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use WechatWorkAppChatBundle\Repository\TextMessageRepository;

#[ORM\Entity(repositoryClass: TextMessageRepository::class)]
#[ORM\Table(name: 'wechat_work_app_chat_text_message', options: ['comment' => '企业微信群聊文本消息'])]
class TextMessage extends BaseChatMessage
{
    #[ORM\Column(type: Types::TEXT, options: ['comment' => '消息内容'])]
    private string $content;

    public function getMsgType(): string
    {
        return 'text';
    }

    public function getRequestContent(): array
    {
        return [
            'text' => [
                'content' => $this->getContent(),
            ],
        ];
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->getId();
    }
}
