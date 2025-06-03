<?php

namespace WechatWorkAppChatBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use WechatWorkAppChatBundle\DependencyInjection\WechatWorkAppChatExtension;

class WechatWorkAppChatExtensionTest extends TestCase
{
    public function test_extension_class_exists(): void
    {
        $this->assertTrue(class_exists(WechatWorkAppChatExtension::class));
    }

    public function test_extension_extends_base_extension(): void
    {
        $reflection = new \ReflectionClass(WechatWorkAppChatExtension::class);
        $this->assertTrue($reflection->isSubclassOf('Symfony\Component\DependencyInjection\Extension\Extension'));
    }

    public function test_load_method_exists(): void
    {
        $reflection = new \ReflectionClass(WechatWorkAppChatExtension::class);
        $this->assertTrue($reflection->hasMethod('load'));
        
        $method = $reflection->getMethod('load');
        $this->assertTrue($method->isPublic());
        
        $parameters = $method->getParameters();
        $this->assertCount(2, $parameters);
        
        $this->assertEquals('configs', $parameters[0]->getName());
        $this->assertEquals('array', $parameters[0]->getType()->getName());
        
        $this->assertEquals('container', $parameters[1]->getName());
        $this->assertEquals('Symfony\Component\DependencyInjection\ContainerBuilder', $parameters[1]->getType()->getName());
        
        // 验证返回类型
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('void', $returnType->getName());
    }

    public function test_load_method_implementation(): void
    {
        $reflection = new \ReflectionClass(WechatWorkAppChatExtension::class);
        $method = $reflection->getMethod('load');
        $methodSource = $this->getMethodSource($method);
        
        // 验证方法实现包含关键逻辑
        $this->assertStringContains('YamlFileLoader', $methodSource);
        $this->assertStringContains('FileLocator', $methodSource);
        $this->assertStringContains('/../Resources/config', $methodSource);
        $this->assertStringContains('services.yaml', $methodSource);
        $this->assertStringContains('load', $methodSource);
    }

    public function test_extension_namespace_is_correct(): void
    {
        $reflection = new \ReflectionClass(WechatWorkAppChatExtension::class);
        $this->assertEquals('WechatWorkAppChatBundle\DependencyInjection', $reflection->getNamespaceName());
    }

    public function test_extension_uses_correct_file_path(): void
    {
        $reflection = new \ReflectionClass(WechatWorkAppChatExtension::class);
        $method = $reflection->getMethod('load');
        $methodSource = $this->getMethodSource($method);
        
        // 验证文件路径设置正确
        $this->assertStringContains('__DIR__', $methodSource);
        $this->assertStringContains('/../Resources/config', $methodSource);
    }

    public function test_extension_loads_services_yaml(): void
    {
        $reflection = new \ReflectionClass(WechatWorkAppChatExtension::class);
        $method = $reflection->getMethod('load');
        $methodSource = $this->getMethodSource($method);
        
        // 验证加载services.yaml文件
        $this->assertStringContains('services.yaml', $methodSource);
    }

    private function getMethodSource(\ReflectionMethod $method): string
    {
        $filename = $method->getFileName();
        $startLine = $method->getStartLine();
        $endLine = $method->getEndLine();
        
        $lines = file($filename);
        $methodLines = array_slice($lines, $startLine - 1, $endLine - $startLine + 1);
        
        return implode('', $methodLines);
    }

    private function assertStringContains(string $needle, string $haystack): void
    {
        $this->assertTrue(
            str_contains($haystack, $needle),
            "Failed asserting that '$haystack' contains '$needle'"
        );
    }
} 