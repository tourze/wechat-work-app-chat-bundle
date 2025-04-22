<?php

namespace WechatWorkAppChatBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '企业微信群聊服务')]
class WechatWorkAppChatBundle extends Bundle
{
}
