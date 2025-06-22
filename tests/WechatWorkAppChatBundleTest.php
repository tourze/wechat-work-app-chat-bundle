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


    public function test_bundle_namespace_is_correct(): void
    {
        $reflection = new \ReflectionClass(WechatWorkAppChatBundle::class);
        $this->assertEquals('WechatWorkAppChatBundle', $reflection->getNamespaceName());
    }
} 