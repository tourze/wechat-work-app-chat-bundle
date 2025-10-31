<?php

namespace WechatWorkAppChatBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;
use WechatWorkAppChatBundle\DependencyInjection\WechatWorkAppChatExtension;

/**
 * @internal
 */
#[CoversClass(WechatWorkAppChatExtension::class)]
final class WechatWorkAppChatExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    private WechatWorkAppChatExtension $extension;

    private ContainerBuilder $container;

    public function testLoadWithTestEnvironment(): void
    {
        $this->container->setParameter('kernel.environment', 'test');

        $this->extension->load([], $this->container);

        // 验证扩展加载了配置
        $this->assertTrue($this->container->hasDefinition('WechatWorkAppChatBundle\Service\AppChatService'));
    }

    public function testLoadWithDevEnvironment(): void
    {
        $this->container->setParameter('kernel.environment', 'dev');

        $this->extension->load([], $this->container);

        // 验证扩展加载了配置
        $this->assertTrue($this->container->hasDefinition('WechatWorkAppChatBundle\Service\AppChatService'));
    }

    public function testLoadWithProdEnvironment(): void
    {
        $this->container->setParameter('kernel.environment', 'prod');

        $this->extension->load([], $this->container);

        // 验证扩展加载了基础配置
        $this->assertTrue($this->container->hasDefinition('WechatWorkAppChatBundle\Service\AppChatService'));
    }

    public function testExtensionConfiguration(): void
    {
        $this->assertInstanceOf(WechatWorkAppChatExtension::class, $this->extension);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = new ContainerBuilder();
        $this->extension = new WechatWorkAppChatExtension();
    }
}
