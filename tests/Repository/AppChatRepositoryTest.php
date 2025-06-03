<?php

namespace WechatWorkAppChatBundle\Tests\Repository;

use PHPUnit\Framework\TestCase;
use WechatWorkAppChatBundle\Repository\AppChatRepository;

class AppChatRepositoryTest extends TestCase
{
    public function test_repository_class_exists(): void
    {
        $this->assertTrue(class_exists(AppChatRepository::class));
    }

    public function test_repository_extends_service_entity_repository(): void
    {
        $reflection = new \ReflectionClass(AppChatRepository::class);
        $this->assertTrue($reflection->isSubclassOf('Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository'));
    }

    public function test_findByChatId_method_exists(): void
    {
        $reflection = new \ReflectionClass(AppChatRepository::class);
        $this->assertTrue($reflection->hasMethod('findByChatId'));
        
        $method = $reflection->getMethod('findByChatId');
        $this->assertTrue($method->isPublic());
        
        $parameters = $method->getParameters();
        $this->assertCount(1, $parameters);
        $this->assertEquals('chatId', $parameters[0]->getName());
        $this->assertEquals('string', $parameters[0]->getType()->getName());
    }

    public function test_findByChatId_return_type(): void
    {
        $reflection = new \ReflectionClass(AppChatRepository::class);
        $method = $reflection->getMethod('findByChatId');
        $returnType = $method->getReturnType();
        
        $this->assertNotNull($returnType);
        $this->assertTrue($returnType->allowsNull());
        $this->assertEquals('WechatWorkAppChatBundle\Entity\AppChat', $returnType->getName());
    }

    public function test_findUnsynced_method_exists(): void
    {
        $reflection = new \ReflectionClass(AppChatRepository::class);
        $this->assertTrue($reflection->hasMethod('findUnsynced'));
        
        $method = $reflection->getMethod('findUnsynced');
        $this->assertTrue($method->isPublic());
        
        $parameters = $method->getParameters();
        $this->assertCount(0, $parameters);
    }

    public function test_findUnsynced_return_type(): void
    {
        $reflection = new \ReflectionClass(AppChatRepository::class);
        $method = $reflection->getMethod('findUnsynced');
        $returnType = $method->getReturnType();
        
        $this->assertNotNull($returnType);
        $this->assertEquals('array', $returnType->getName());
    }

    public function test_repository_has_correct_entity_class(): void
    {
        $reflection = new \ReflectionClass(AppChatRepository::class);
        $constructor = $reflection->getConstructor();
        $constructorSource = $this->getMethodSource($constructor);
        
        // 验证构造函数调用parent::__construct时传递了正确的实体类
        $this->assertStringContains('AppChat::class', $constructorSource);
    }

    public function test_inherited_methods_availability(): void
    {
        $reflection = new \ReflectionClass(AppChatRepository::class);
        
        // 测试继承的基本方法是否可用
        $this->assertTrue($reflection->hasMethod('find'));
        $this->assertTrue($reflection->hasMethod('findOneBy'));
        $this->assertTrue($reflection->hasMethod('findAll'));
        $this->assertTrue($reflection->hasMethod('findBy'));
    }

    public function test_findByChatId_method_implementation(): void
    {
        $reflection = new \ReflectionClass(AppChatRepository::class);
        $method = $reflection->getMethod('findByChatId');
        $methodSource = $this->getMethodSource($method);
        
        // 验证方法实现调用了findOneBy
        $this->assertStringContains('findOneBy', $methodSource);
        $this->assertStringContains('chatId', $methodSource);
    }

    public function test_findUnsynced_method_implementation(): void
    {
        $reflection = new \ReflectionClass(AppChatRepository::class);
        $method = $reflection->getMethod('findUnsynced');
        $methodSource = $this->getMethodSource($method);
        
        // 验证方法实现使用了正确的查询构建
        $this->assertStringContains('createQueryBuilder', $methodSource);
        $this->assertStringContains('isSynced', $methodSource);
        $this->assertStringContains('false', $methodSource);
        $this->assertStringContains('getQuery', $methodSource);
        $this->assertStringContains('getResult', $methodSource);
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