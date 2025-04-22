<?php

namespace WechatWorkAppChatBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '企业微信群聊服务')]
class WechatWorkAppChatBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            \WechatWorkBundle\WechatWorkBundle::class => ['all' => true],
        ];
    }
}
