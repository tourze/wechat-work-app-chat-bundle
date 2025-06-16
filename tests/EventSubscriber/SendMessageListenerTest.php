<?php

namespace WechatWorkAppChatBundle\Tests\EventSubscriber;

use PHPUnit\Framework\TestCase;
use WechatWorkAppChatBundle\EventSubscriber\SendMessageListener;

class SendMessageListenerTest extends TestCase
{
    public function test_listener_class_exists(): void
    {
        $this->assertTrue(class_exists(SendMessageListener::class));
    }

    public function test_listener_has_required_constructor_parameters(): void
    {
        $reflection = new \ReflectionClass(SendMessageListener::class);
        $constructor = $reflection->getConstructor();
        
        $this->assertNotNull($constructor);
        
        $parameters = $constructor->getParameters();
        $this->assertCount(2, $parameters);
        
        $this->assertEquals('workService', $parameters[0]->getName());
        $this->assertEquals('WechatWorkBundle\Service\WorkService', $parameters[0]->getType()->getName());
        
        $this->assertEquals('logger', $parameters[1]->getName());
        $this->assertEquals('Psr\Log\LoggerInterface', $parameters[1]->getType()->getName());
    }

    public function test_listener_has_entity_listener_attributes(): void
    {
        $reflection = new \ReflectionClass(SendMessageListener::class);
        $attributes = $reflection->getAttributes();
        
        $this->assertNotEmpty($attributes);
        $this->assertCount(4, $attributes); // 4个实体监听器属性
        
        $entityListenerAttributes = [];
        foreach ($attributes as $attribute) {
            if ($attribute->getName() === 'Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener') {
                $entityListenerAttributes[] = $attribute;
            }
        }
        
        $this->assertCount(4, $entityListenerAttributes);
        
        // 验证每个属性的参数
        $expectedEntities = [
            'WechatWorkAppChatBundle\Entity\TextMessage',
            'WechatWorkAppChatBundle\Entity\MarkdownMessage',
            'WechatWorkAppChatBundle\Entity\ImageMessage',
            'WechatWorkAppChatBundle\Entity\FileMessage'
        ];
        
        $actualEntities = [];
        foreach ($entityListenerAttributes as $attribute) {
            $arguments = $attribute->getArguments();
            $this->assertEquals('postPersist', $arguments['event']);
            $this->assertEquals('postPersist', $arguments['method']);
            $actualEntities[] = $arguments['entity'];
        }
        
        foreach ($expectedEntities as $expectedEntity) {
            $this->assertContains($expectedEntity, $actualEntities);
        }
    }

    public function test_postPersist_method_exists(): void
    {
        $reflection = new \ReflectionClass(SendMessageListener::class);
        $this->assertTrue($reflection->hasMethod('postPersist'));
        
        $method = $reflection->getMethod('postPersist');
        $this->assertTrue($method->isPublic());
        
        $parameters = $method->getParameters();
        $this->assertCount(2, $parameters);
        
        $this->assertEquals('message', $parameters[0]->getName());
        $this->assertEquals('WechatWorkAppChatBundle\Entity\BaseChatMessage', $parameters[0]->getType()->getName());
        
        $this->assertEquals('args', $parameters[1]->getName());
        $this->assertEquals('Doctrine\ORM\Event\PostPersistEventArgs', $parameters[1]->getType()->getName());
        
        // 验证返回类型
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('void', $returnType->getName());
    }

    public function test_postPersist_method_has_throws_annotation(): void
    {
        $reflection = new \ReflectionClass(SendMessageListener::class);
        $method = $reflection->getMethod('postPersist');
        $docComment = $method->getDocComment();
        
        $this->assertNotFalse($docComment);
        $this->assertStringContains('@throws', $docComment);
        $this->assertStringContains('\\Throwable', $docComment);
    }

    public function test_postPersist_method_implementation(): void
    {
        $reflection = new \ReflectionClass(SendMessageListener::class);
        $method = $reflection->getMethod('postPersist');
        $methodSource = $this->getMethodSource($method);
        
        // 验证方法实现包含关键逻辑
        $this->assertStringContains('try', $methodSource);
        $this->assertStringContains('catch', $methodSource);
        $this->assertStringContains('SendAppChatMessageRequest', $methodSource);
        $this->assertStringContains('workService', $methodSource);
        $this->assertStringContains('setAgent', $methodSource);
        $this->assertStringContains('setMessage', $methodSource);
        $this->assertStringContains('request', $methodSource);
    }

    public function test_postPersist_success_handling(): void
    {
        $reflection = new \ReflectionClass(SendMessageListener::class);
        $method = $reflection->getMethod('postPersist');
        $methodSource = $this->getMethodSource($method);
        
        // 验证成功处理逻辑
        $this->assertStringContains('msgid', $methodSource);
        $this->assertStringContains('setMsgId', $methodSource);
        $this->assertStringContains('setIsSent', $methodSource);
        $this->assertStringContains('setSentAt', $methodSource);
        $this->assertStringContains('DateTimeImmutable', $methodSource);
        $this->assertStringContains('flush', $methodSource);
    }

    public function test_postPersist_error_handling(): void
    {
        $reflection = new \ReflectionClass(SendMessageListener::class);
        $method = $reflection->getMethod('postPersist');
        $methodSource = $this->getMethodSource($method);
        
        // 验证错误处理逻辑
        $this->assertStringContains('Throwable', $methodSource);
        $this->assertStringContains('logger', $methodSource);
        $this->assertStringContains('error', $methodSource);
        $this->assertStringContains('自动发送群聊消息失败', $methodSource);
        $this->assertStringContains('chat_id', $methodSource);
        $this->assertStringContains('getChatId', $methodSource);
        $this->assertStringContains('getMessage', $methodSource);
    }

    public function test_listener_namespace_is_correct(): void
    {
        $reflection = new \ReflectionClass(SendMessageListener::class);
        $this->assertEquals('WechatWorkAppChatBundle\EventSubscriber', $reflection->getNamespaceName());
    }

    public function test_listener_has_required_properties(): void
    {
        $reflection = new \ReflectionClass(SendMessageListener::class);
        $constructor = $reflection->getConstructor();
        $constructorSource = $this->getMethodSource($constructor);
        
        // 验证构造函数中使用了promoted properties
        $this->assertStringContains('private readonly WorkService', $constructorSource);
        $this->assertStringContains('private readonly LoggerInterface', $constructorSource);
    }

    public function test_listener_uses_correct_imports(): void
    {
        $reflection = new \ReflectionClass(SendMessageListener::class);
        $filename = $reflection->getFileName();
        $fileContent = file_get_contents($filename);
        
        // 验证关键import语句
        $this->assertStringContains('use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener', $fileContent);
        $this->assertStringContains('use Doctrine\ORM\Event\PostPersistEventArgs', $fileContent);
        $this->assertStringContains('use Doctrine\ORM\Events', $fileContent);
        $this->assertStringContains('use Psr\Log\LoggerInterface', $fileContent);
        $this->assertStringContains('use WechatWorkAppChatBundle\Entity\BaseChatMessage', $fileContent);
        $this->assertStringContains('use WechatWorkAppChatBundle\Request\SendAppChatMessageRequest', $fileContent);
        $this->assertStringContains('use WechatWorkBundle\Service\WorkService', $fileContent);
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