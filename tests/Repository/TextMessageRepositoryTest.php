<?php

namespace WechatWorkAppChatBundle\Tests\Repository;

use PHPUnit\Framework\TestCase;
use WechatWorkAppChatBundle\Repository\TextMessageRepository;

class TextMessageRepositoryTest extends TestCase
{
    public function test_repository_class_exists(): void
    {
        $this->assertTrue(class_exists(TextMessageRepository::class));
    }

    public function test_repository_extends_service_entity_repository(): void
    {
        $reflection = new \ReflectionClass(TextMessageRepository::class);
        $this->assertTrue($reflection->isSubclassOf('Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository'));
    }

    public function test_findUnsent_method_exists(): void
    {
        $reflection = new \ReflectionClass(TextMessageRepository::class);
        $this->assertTrue($reflection->hasMethod('findUnsent'));
        
        $method = $reflection->getMethod('findUnsent');
        $this->assertTrue($method->isPublic());
        
        $parameters = $method->getParameters();
        $this->assertCount(0, $parameters);
    }

    public function test_findUnsent_return_type(): void
    {
        $reflection = new \ReflectionClass(TextMessageRepository::class);
        $method = $reflection->getMethod('findUnsent');
        $returnType = $method->getReturnType();
        
        $this->assertNotNull($returnType);
        $this->assertEquals('array', (string)$returnType);
    }

    public function test_repository_has_correct_entity_class(): void
    {
        $reflection = new \ReflectionClass(TextMessageRepository::class);
        $constructor = $reflection->getConstructor();
        $constructorSource = $this->getMethodSource($constructor);
        
        // 验证构造函数调用parent::__construct时传递了正确的实体类
        $this->assertStringContains('TextMessage::class', $constructorSource);
    }

    public function test_inherited_methods_availability(): void
    {
        $reflection = new \ReflectionClass(TextMessageRepository::class);
        
        // 测试继承的基本方法是否可用
        $this->assertTrue($reflection->hasMethod('find'));
        $this->assertTrue($reflection->hasMethod('findOneBy'));
        $this->assertTrue($reflection->hasMethod('findAll'));
        $this->assertTrue($reflection->hasMethod('findBy'));
    }

    public function test_findUnsent_method_implementation(): void
    {
        $reflection = new \ReflectionClass(TextMessageRepository::class);
        $method = $reflection->getMethod('findUnsent');
        $methodSource = $this->getMethodSource($method);
        
        // 验证方法实现使用了正确的查询构建
        $this->assertStringContains('createQueryBuilder', $methodSource);
        $this->assertStringContains('tm', $methodSource); // 查询别名
        $this->assertStringContains('isSent', $methodSource);
        $this->assertStringContains('false', $methodSource);
        $this->assertStringContains('orderBy', $methodSource);
        $this->assertStringContains('ASC', $methodSource);
        $this->assertStringContains('getQuery', $methodSource);
        $this->assertStringContains('getResult', $methodSource);
    }

    public function test_findUnsent_has_correct_order_by(): void
    {
        $reflection = new \ReflectionClass(TextMessageRepository::class);
        $method = $reflection->getMethod('findUnsent');
        $methodSource = $this->getMethodSource($method);
        
        // 验证排序是按照id升序
        $this->assertStringContains('tm.id', $methodSource);
        $this->assertStringContains('ASC', $methodSource);
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