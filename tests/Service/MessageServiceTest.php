<?php

namespace WechatWorkAppChatBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use WechatWorkAppChatBundle\Service\MessageService;

class MessageServiceTest extends TestCase
{
    public function test_service_class_exists(): void
    {
        $this->assertTrue(class_exists(MessageService::class));
    }

    public function test_service_has_required_constructor_parameters(): void
    {
        $reflection = new \ReflectionClass(MessageService::class);
        $constructor = $reflection->getConstructor();
        
        $this->assertNotNull($constructor);
        
        $parameters = $constructor->getParameters();
        $this->assertCount(7, $parameters);
        
        // 验证构造函数参数类型
        $this->assertEquals('entityManager', $parameters[0]->getName());
        $this->assertEquals('Doctrine\ORM\EntityManagerInterface', (string)$parameters[0]->getType());
        
        $this->assertEquals('textMessageRepository', $parameters[1]->getName());
        $this->assertEquals('WechatWorkAppChatBundle\Repository\TextMessageRepository', (string)$parameters[1]->getType());
        
        $this->assertEquals('markdownMessageRepository', $parameters[2]->getName());
        $this->assertEquals('WechatWorkAppChatBundle\Repository\MarkdownMessageRepository', (string)$parameters[2]->getType());
        
        $this->assertEquals('imageMessageRepository', $parameters[3]->getName());
        $this->assertEquals('WechatWorkAppChatBundle\Repository\ImageMessageRepository', (string)$parameters[3]->getType());
        
        $this->assertEquals('fileMessageRepository', $parameters[4]->getName());
        $this->assertEquals('WechatWorkAppChatBundle\Repository\FileMessageRepository', (string)$parameters[4]->getType());
        
        $this->assertEquals('workService', $parameters[5]->getName());
        $this->assertEquals('WechatWorkBundle\Service\WorkService', (string)$parameters[5]->getType());
        
        $this->assertEquals('logger', $parameters[6]->getName());
        $this->assertEquals('Psr\Log\LoggerInterface', (string)$parameters[6]->getType());
    }

    public function test_sendText_method_exists(): void
    {
        $reflection = new \ReflectionClass(MessageService::class);
        $this->assertTrue($reflection->hasMethod('sendText'));
        
        $method = $reflection->getMethod('sendText');
        $this->assertTrue($method->isPublic());
        
        $parameters = $method->getParameters();
        $this->assertCount(2, $parameters);
        
        $this->assertEquals('appChat', $parameters[0]->getName());
        $this->assertEquals('WechatWorkAppChatBundle\Entity\AppChat', (string)$parameters[0]->getType());
        
        $this->assertEquals('content', $parameters[1]->getName());
        $this->assertEquals('string', (string)$parameters[1]->getType());
        
        // 验证返回类型
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('WechatWorkAppChatBundle\Entity\TextMessage', (string)$returnType);
    }

    public function test_sendMarkdown_method_exists(): void
    {
        $reflection = new \ReflectionClass(MessageService::class);
        $this->assertTrue($reflection->hasMethod('sendMarkdown'));
        
        $method = $reflection->getMethod('sendMarkdown');
        $this->assertTrue($method->isPublic());
        
        $parameters = $method->getParameters();
        $this->assertCount(2, $parameters);
        
        $this->assertEquals('appChat', $parameters[0]->getName());
        $this->assertEquals('WechatWorkAppChatBundle\Entity\AppChat', (string)$parameters[0]->getType());
        
        $this->assertEquals('content', $parameters[1]->getName());
        $this->assertEquals('string', (string)$parameters[1]->getType());
        
        // 验证返回类型
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('WechatWorkAppChatBundle\Entity\MarkdownMessage', (string)$returnType);
    }

    public function test_sendImage_method_exists(): void
    {
        $reflection = new \ReflectionClass(MessageService::class);
        $this->assertTrue($reflection->hasMethod('sendImage'));
        
        $method = $reflection->getMethod('sendImage');
        $this->assertTrue($method->isPublic());
        
        $parameters = $method->getParameters();
        $this->assertCount(2, $parameters);
        
        $this->assertEquals('appChat', $parameters[0]->getName());
        $this->assertEquals('WechatWorkAppChatBundle\Entity\AppChat', (string)$parameters[0]->getType());
        
        $this->assertEquals('mediaId', $parameters[1]->getName());
        $this->assertEquals('string', (string)$parameters[1]->getType());
        
        // 验证返回类型
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('WechatWorkAppChatBundle\Entity\ImageMessage', (string)$returnType);
    }

    public function test_sendFile_method_exists(): void
    {
        $reflection = new \ReflectionClass(MessageService::class);
        $this->assertTrue($reflection->hasMethod('sendFile'));
        
        $method = $reflection->getMethod('sendFile');
        $this->assertTrue($method->isPublic());
        
        $parameters = $method->getParameters();
        $this->assertCount(2, $parameters);
        
        $this->assertEquals('appChat', $parameters[0]->getName());
        $this->assertEquals('WechatWorkAppChatBundle\Entity\AppChat', (string)$parameters[0]->getType());
        
        $this->assertEquals('mediaId', $parameters[1]->getName());
        $this->assertEquals('string', (string)$parameters[1]->getType());
        
        // 验证返回类型
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('WechatWorkAppChatBundle\Entity\FileMessage', (string)$returnType);
    }

    public function test_sendUnsent_method_exists(): void
    {
        $reflection = new \ReflectionClass(MessageService::class);
        $this->assertTrue($reflection->hasMethod('sendUnsent'));
        
        $method = $reflection->getMethod('sendUnsent');
        $this->assertTrue($method->isPublic());
        
        $parameters = $method->getParameters();
        $this->assertCount(0, $parameters);
        
        // 验证返回类型
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('void', (string)$returnType);
    }

    public function test_sendText_method_implementation(): void
    {
        $reflection = new \ReflectionClass(MessageService::class);
        $method = $reflection->getMethod('sendText');
        $methodSource = $this->getMethodSource($method);
        
        // 验证方法实现包含关键逻辑
        $this->assertStringContains('new TextMessage', $methodSource);
        $this->assertStringContains('setAppChat', $methodSource);
        $this->assertStringContains('setContent', $methodSource);
        $this->assertStringContains('persist', $methodSource);
        $this->assertStringContains('flush', $methodSource);
    }

    public function test_sendMarkdown_method_implementation(): void
    {
        $reflection = new \ReflectionClass(MessageService::class);
        $method = $reflection->getMethod('sendMarkdown');
        $methodSource = $this->getMethodSource($method);
        
        // 验证方法实现包含关键逻辑
        $this->assertStringContains('new MarkdownMessage', $methodSource);
        $this->assertStringContains('setAppChat', $methodSource);
        $this->assertStringContains('setContent', $methodSource);
        $this->assertStringContains('persist', $methodSource);
        $this->assertStringContains('flush', $methodSource);
    }

    public function test_sendImage_method_implementation(): void
    {
        $reflection = new \ReflectionClass(MessageService::class);
        $method = $reflection->getMethod('sendImage');
        $methodSource = $this->getMethodSource($method);
        
        // 验证方法实现包含关键逻辑
        $this->assertStringContains('new ImageMessage', $methodSource);
        $this->assertStringContains('setAppChat', $methodSource);
        $this->assertStringContains('setMediaId', $methodSource);
        $this->assertStringContains('persist', $methodSource);
        $this->assertStringContains('flush', $methodSource);
    }

    public function test_sendFile_method_implementation(): void
    {
        $reflection = new \ReflectionClass(MessageService::class);
        $method = $reflection->getMethod('sendFile');
        $methodSource = $this->getMethodSource($method);
        
        // 验证方法实现包含关键逻辑
        $this->assertStringContains('new FileMessage', $methodSource);
        $this->assertStringContains('setAppChat', $methodSource);
        $this->assertStringContains('setMediaId', $methodSource);
        $this->assertStringContains('persist', $methodSource);
        $this->assertStringContains('flush', $methodSource);
    }

    public function test_sendUnsent_method_implementation(): void
    {
        $reflection = new \ReflectionClass(MessageService::class);
        $method = $reflection->getMethod('sendUnsent');
        $methodSource = $this->getMethodSource($method);
        
        // 验证方法实现包含关键逻辑
        $this->assertStringContains('array_merge', $methodSource);
        $this->assertStringContains('findUnsent', $methodSource);
        $this->assertStringContains('foreach', $methodSource);
        $this->assertStringContains('SendAppChatMessageRequest', $methodSource);
        $this->assertStringContains('workService', $methodSource);
        $this->assertStringContains('setMsgId', $methodSource);
        $this->assertStringContains('setIsSent', $methodSource);
        $this->assertStringContains('setSentAt', $methodSource);
        $this->assertStringContains('try', $methodSource);
        $this->assertStringContains('catch', $methodSource);
        $this->assertStringContains('logger', $methodSource);
        $this->assertStringContains('continue', $methodSource);
    }

    public function test_service_has_required_properties(): void
    {
        $reflection = new \ReflectionClass(MessageService::class);
        $constructor = $reflection->getConstructor();
        $constructorSource = $this->getMethodSource($constructor);
        
        // 验证构造函数中使用了promoted properties
        $this->assertStringContains('private readonly EntityManagerInterface', $constructorSource);
        $this->assertStringContains('private readonly TextMessageRepository', $constructorSource);
        $this->assertStringContains('private readonly MarkdownMessageRepository', $constructorSource);
        $this->assertStringContains('private readonly ImageMessageRepository', $constructorSource);
        $this->assertStringContains('private readonly FileMessageRepository', $constructorSource);
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
