<?php

namespace WechatWorkAppChatBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use WechatWorkAppChatBundle\Repository\ImageMessageRepository;

#[ORM\Entity(repositoryClass: ImageMessageRepository::class)]
#[ORM\Table(name: 'wechat_work_app_chat_image_message', options: ['comment' => '企业微信群聊图片消息'])]
class ImageMessage extends BaseChatMessage
{
    #[ORM\Column(type: Types::STRING, length: 128, options: ['comment' => '图片素材ID'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 128)]
    private string $mediaId;

    public function getMsgType(): string
    {
        return 'image';
    }

    /**
     * @return array<string, mixed>
     */
    public function getRequestContent(): array
    {
        return [
            'image' => [
                'media_id' => $this->mediaId,
            ],
        ];
    }

    public function getMediaId(): string
    {
        return $this->mediaId;
    }

    public function setMediaId(string $mediaId): void
    {
        $this->mediaId = $mediaId;
    }

    public function __toString(): string
    {
        return (string) $this->getId();
    }
}
