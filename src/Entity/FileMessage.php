<?php

namespace WechatWorkAppChatBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use WechatWorkAppChatBundle\Repository\FileMessageRepository;

#[ORM\Entity(repositoryClass: FileMessageRepository::class)]
#[ORM\Table(name: 'wechat_work_app_chat_file_message', options: ['comment' => '企业微信群聊文件消息'])]
class FileMessage extends BaseChatMessage
{
    #[ORM\Column(type: Types::STRING, length: 128, options: ['comment' => '文件素材ID'])]
    private string $mediaId;

    public function getMsgType(): string
    {
        return 'file';
    }

    public function getRequestContent(): array
    {
        return [
            'file' => [
                'media_id' => $this->mediaId,
            ],
        ];
    }

    public function getMediaId(): string
    {
        return $this->mediaId;
    }

    public function setMediaId(string $mediaId): self
    {
        $this->mediaId = $mediaId;

        return $this;
    }
}
