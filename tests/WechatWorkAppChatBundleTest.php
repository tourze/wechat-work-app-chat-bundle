<?php

namespace WechatWorkAppChatBundle\Tests;

use PHPUnit\Framework\TestCase;
use WechatWorkAppChatBundle\WechatWorkAppChatBundle;

class WechatWorkAppChatBundleTest extends TestCase
{
    public function test_bundle_class_exists(): void
    {
        $this->assertTrue(class_exists(WechatWorkAppChatBundle::class));
    }

    public function test_bundle_extends_base_bundle(): void
    {
        $reflection = new \ReflectionClass(WechatWorkAppChatBundle::class);
        $this->assertTrue($reflection->isSubclassOf('Symfony\Component\HttpKernel\Bundle\Bundle'));
    }

    public function test_bundle_has_permission_attribute(): void
    {
        $reflection = new \ReflectionClass(WechatWorkAppChatBundle::class);
        $attributes = $reflection->getAttributes();
        
        $this->assertNotEmpty($attributes);
        
        $permissionAttribute = null;
        foreach ($attributes as $attribute) {
            if ($attribute->getName() === 'Tourze\EasyAdmin\Attribute\Permission\AsPermission') {
                $permissionAttribute = $attribute;
                break;
            }
        }
        
        $this->assertNotNull($permissionAttribute);
        
        $arguments = $permissionAttribute->getArguments();
        $this->assertEquals('企业微信群聊服务', $arguments['title']);
    }

    public function test_bundle_namespace_is_correct(): void
    {
        $reflection = new \ReflectionClass(WechatWorkAppChatBundle::class);
        $this->assertEquals('WechatWorkAppChatBundle', $reflection->getNamespaceName());
    }
} 