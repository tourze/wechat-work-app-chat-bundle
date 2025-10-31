<?php

declare(strict_types=1);

namespace WechatWorkAppChatBundle\Service;

use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Entity\FileMessage;
use WechatWorkAppChatBundle\Entity\ImageMessage;
use WechatWorkAppChatBundle\Entity\MarkdownMessage;
use WechatWorkAppChatBundle\Entity\TextMessage;

#[Autoconfigure(public: true)]
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(private LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        // 创建企业微信管理顶级菜单
        if (null === $item->getChild('企业微信管理')) {
            $item->addChild('企业微信管理')
                ->setAttribute('icon', 'fa fa-wechat')
            ;
        }

        $wechatMenu = $item->getChild('企业微信管理');
        if (null === $wechatMenu) {
            return;
        }

        // 检查是否已存在群聊管理子菜单
        if (null === $wechatMenu->getChild('群聊管理')) {
            $wechatMenu->addChild('群聊管理')
                ->setAttribute('icon', 'fa fa-comments')
            ;
        }

        $appChatMenu = $wechatMenu->getChild('群聊管理');
        if (null === $appChatMenu) {
            return;
        }

        // 群聊会话管理
        $appChatMenu
            ->addChild('群聊会话')
            ->setUri($this->linkGenerator->getCurdListPage(AppChat::class))
            ->setAttribute('icon', 'fa fa-comments-o')
        ;

        // 文本消息管理
        $appChatMenu
            ->addChild('文本消息')
            ->setUri($this->linkGenerator->getCurdListPage(TextMessage::class))
            ->setAttribute('icon', 'fa fa-font')
        ;

        // 图片消息管理
        $appChatMenu
            ->addChild('图片消息')
            ->setUri($this->linkGenerator->getCurdListPage(ImageMessage::class))
            ->setAttribute('icon', 'fa fa-image')
        ;

        // 文件消息管理
        $appChatMenu
            ->addChild('文件消息')
            ->setUri($this->linkGenerator->getCurdListPage(FileMessage::class))
            ->setAttribute('icon', 'fa fa-file')
        ;

        // Markdown消息管理
        $appChatMenu
            ->addChild('Markdown消息')
            ->setUri($this->linkGenerator->getCurdListPage(MarkdownMessage::class))
            ->setAttribute('icon', 'fa fa-code')
        ;
    }
}
