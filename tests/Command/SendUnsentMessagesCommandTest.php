<?php

namespace WechatWorkAppChatBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use WechatWorkAppChatBundle\Command\SendUnsentMessagesCommand;

class SendUnsentMessagesCommandTest extends TestCase
{
    public function test_command_class_exists(): void
    {
        $this->assertTrue(class_exists(SendUnsentMessagesCommand::class));
    }

    public function test_command_extends_base_command(): void
    {
        $reflection = new \ReflectionClass(SendUnsentMessagesCommand::class);
        $this->assertTrue($reflection->isSubclassOf('Symfony\Component\Console\Command\Command'));
    }

    public function test_command_has_required_constructor_parameter(): void
    {
        $reflection = new \ReflectionClass(SendUnsentMessagesCommand::class);
        $constructor = $reflection->getConstructor();
        
        $this->assertNotNull($constructor);
        
        $parameters = $constructor->getParameters();
        $this->assertCount(1, $parameters);
        
        $this->assertEquals('messageService', $parameters[0]->getName());
        $this->assertEquals('WechatWorkAppChatBundle\Service\MessageService', $parameters[0]->getType()->getName());
    }

    public function test_command_has_attribute_configuration(): void
    {
        $reflection = new \ReflectionClass(SendUnsentMessagesCommand::class);
        $attributes = $reflection->getAttributes();
        
        $this->assertNotEmpty($attributes);
        
        $asCommandAttribute = null;
        foreach ($attributes as $attribute) {
            if ($attribute->getName() === 'Symfony\Component\Console\Attribute\AsCommand') {
                $asCommandAttribute = $attribute;
                break;
            }
        }
        
        $this->assertNotNull($asCommandAttribute);
        
        $arguments = $asCommandAttribute->getArguments();
        $this->assertEquals('wechat-work:app-chat:send-unsent', $arguments['name']);
        $this->assertEquals('发送未发送的企业微信群聊消息', $arguments['description']);
    }

    public function test_execute_method_exists(): void
    {
        $reflection = new \ReflectionClass(SendUnsentMessagesCommand::class);
        $this->assertTrue($reflection->hasMethod('execute'));
        
        $method = $reflection->getMethod('execute');
        $this->assertTrue($method->isProtected());
        
        $parameters = $method->getParameters();
        $this->assertCount(2, $parameters);
        
        $this->assertEquals('input', $parameters[0]->getName());
        $this->assertEquals('output', $parameters[1]->getName());
        
        // 验证返回类型
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('int', $returnType->getName());
    }

    public function test_execute_method_implementation(): void
    {
        $reflection = new \ReflectionClass(SendUnsentMessagesCommand::class);
        $method = $reflection->getMethod('execute');
        $methodSource = $this->getMethodSource($method);
        
        // 验证方法实现包含关键逻辑
        $this->assertStringContains('writeln', $methodSource);
        $this->assertStringContains('messageService', $methodSource);
        $this->assertStringContains('sendUnsent', $methodSource);
        $this->assertStringContains('try', $methodSource);
        $this->assertStringContains('catch', $methodSource);
        $this->assertStringContains('Command::SUCCESS', $methodSource);
        $this->assertStringContains('Command::FAILURE', $methodSource);
    }

    public function test_command_handles_exceptions(): void
    {
        $reflection = new \ReflectionClass(SendUnsentMessagesCommand::class);
        $method = $reflection->getMethod('execute');
        $methodSource = $this->getMethodSource($method);
        
        // 验证异常处理
        $this->assertStringContains('Throwable', $methodSource);
        $this->assertStringContains('getMessage', $methodSource);
        $this->assertStringContains('发送失败', $methodSource);
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