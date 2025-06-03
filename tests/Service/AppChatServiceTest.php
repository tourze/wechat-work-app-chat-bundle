<?php

namespace WechatWorkAppChatBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use WechatWorkAppChatBundle\Service\AppChatService;

class AppChatServiceTest extends TestCase
{
    public function test_service_class_exists(): void
    {
        $this->assertTrue(class_exists(AppChatService::class));
    }

    public function test_service_has_required_constructor_parameters(): void
    {
        $reflection = new \ReflectionClass(AppChatService::class);
        $constructor = $reflection->getConstructor();
        
        $this->assertNotNull($constructor);
        
        $parameters = $constructor->getParameters();
        $this->assertCount(4, $parameters);
        
        // 验证构造函数参数类型
        $this->assertEquals('entityManager', $parameters[0]->getName());
        $this->assertEquals('Doctrine\ORM\EntityManagerInterface', $parameters[0]->getType()->getName());
        
        $this->assertEquals('appChatRepository', $parameters[1]->getName());
        $this->assertEquals('WechatWorkAppChatBundle\Repository\AppChatRepository', $parameters[1]->getType()->getName());
        
        $this->assertEquals('workService', $parameters[2]->getName());
        $this->assertEquals('WechatWorkBundle\Service\WorkService', $parameters[2]->getType()->getName());
        
        $this->assertEquals('logger', $parameters[3]->getName());
        $this->assertEquals('Psr\Log\LoggerInterface', $parameters[3]->getType()->getName());
    }

    public function test_createAppChat_method_exists(): void
    {
        $reflection = new \ReflectionClass(AppChatService::class);
        $this->assertTrue($reflection->hasMethod('createAppChat'));
        
        $method = $reflection->getMethod('createAppChat');
        $this->assertTrue($method->isPublic());
        
        $parameters = $method->getParameters();
        $this->assertCount(4, $parameters);
        
        $this->assertEquals('agent', $parameters[0]->getName());
        $this->assertEquals('name', $parameters[1]->getName());
        $this->assertEquals('owner', $parameters[2]->getName());
        $this->assertEquals('userList', $parameters[3]->getName());
        
        // 验证返回类型
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('WechatWorkAppChatBundle\Entity\AppChat', $returnType->getName());
    }

    public function test_updateAppChat_method_exists(): void
    {
        $reflection = new \ReflectionClass(AppChatService::class);
        $this->assertTrue($reflection->hasMethod('updateAppChat'));
        
        $method = $reflection->getMethod('updateAppChat');
        $this->assertTrue($method->isPublic());
        
        $parameters = $method->getParameters();
        $this->assertCount(1, $parameters);
        
        $this->assertEquals('appChat', $parameters[0]->getName());
        $this->assertEquals('WechatWorkAppChatBundle\Entity\AppChat', $parameters[0]->getType()->getName());
        
        // 验证返回类型
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('void', $returnType->getName());
    }

    public function test_syncAppChat_method_exists(): void
    {
        $reflection = new \ReflectionClass(AppChatService::class);
        $this->assertTrue($reflection->hasMethod('syncAppChat'));
        
        $method = $reflection->getMethod('syncAppChat');
        $this->assertTrue($method->isPublic());
        
        $parameters = $method->getParameters();
        $this->assertCount(1, $parameters);
        
        $this->assertEquals('appChat', $parameters[0]->getName());
        $this->assertEquals('WechatWorkAppChatBundle\Entity\AppChat', $parameters[0]->getType()->getName());
        
        // 验证返回类型
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('void', $returnType->getName());
    }

    public function test_syncUnsynced_method_exists(): void
    {
        $reflection = new \ReflectionClass(AppChatService::class);
        $this->assertTrue($reflection->hasMethod('syncUnsynced'));
        
        $method = $reflection->getMethod('syncUnsynced');
        $this->assertTrue($method->isPublic());
        
        $parameters = $method->getParameters();
        $this->assertCount(0, $parameters);
        
        // 验证返回类型
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('void', $returnType->getName());
    }

    public function test_createAppChat_method_implementation(): void
    {
        $reflection = new \ReflectionClass(AppChatService::class);
        $method = $reflection->getMethod('createAppChat');
        $methodSource = $this->getMethodSource($method);
        
        // 验证方法实现包含关键逻辑
        $this->assertStringContains('CreateAppChatRequest', $methodSource);
        $this->assertStringContains('workService', $methodSource);
        $this->assertStringContains('new AppChat', $methodSource);
        $this->assertStringContains('persist', $methodSource);
        $this->assertStringContains('flush', $methodSource);
        $this->assertStringContains('setIsSynced', $methodSource);
        $this->assertStringContains('setLastSyncedAt', $methodSource);
    }

    public function test_updateAppChat_method_implementation(): void
    {
        $reflection = new \ReflectionClass(AppChatService::class);
        $method = $reflection->getMethod('updateAppChat');
        $methodSource = $this->getMethodSource($method);
        
        // 验证方法实现包含关键逻辑
        $this->assertStringContains('UpdateAppChatRequest', $methodSource);
        $this->assertStringContains('workService', $methodSource);
        $this->assertStringContains('setIsSynced', $methodSource);
        $this->assertStringContains('setLastSyncedAt', $methodSource);
        $this->assertStringContains('flush', $methodSource);
    }

    public function test_syncAppChat_method_implementation(): void
    {
        $reflection = new \ReflectionClass(AppChatService::class);
        $method = $reflection->getMethod('syncAppChat');
        $methodSource = $this->getMethodSource($method);
        
        // 验证方法实现包含关键逻辑
        $this->assertStringContains('GetAppChatRequest', $methodSource);
        $this->assertStringContains('workService', $methodSource);
        $this->assertStringContains('try', $methodSource);
        $this->assertStringContains('catch', $methodSource);
        $this->assertStringContains('logger', $methodSource);
        $this->assertStringContains('setName', $methodSource);
        $this->assertStringContains('setOwner', $methodSource);
        $this->assertStringContains('setUserList', $methodSource);
    }

    public function test_syncUnsynced_method_implementation(): void
    {
        $reflection = new \ReflectionClass(AppChatService::class);
        $method = $reflection->getMethod('syncUnsynced');
        $methodSource = $this->getMethodSource($method);
        
        // 验证方法实现包含关键逻辑
        $this->assertStringContains('appChatRepository', $methodSource);
        $this->assertStringContains('findUnsynced', $methodSource);
        $this->assertStringContains('foreach', $methodSource);
        $this->assertStringContains('syncAppChat', $methodSource);
        $this->assertStringContains('try', $methodSource);
        $this->assertStringContains('catch', $methodSource);
        $this->assertStringContains('continue', $methodSource);
    }

    public function test_service_has_required_properties(): void
    {
        $reflection = new \ReflectionClass(AppChatService::class);
        $constructor = $reflection->getConstructor();
        $constructorSource = $this->getMethodSource($constructor);
        
        // 验证构造函数中使用了promoted properties
        $this->assertStringContains('private readonly EntityManagerInterface', $constructorSource);
        $this->assertStringContains('private readonly AppChatRepository', $constructorSource);
        $this->assertStringContains('private readonly WorkService', $constructorSource);
        $this->assertStringContains('private readonly LoggerInterface', $constructorSource);
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