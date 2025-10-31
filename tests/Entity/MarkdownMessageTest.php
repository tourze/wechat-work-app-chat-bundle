<?php

namespace WechatWorkAppChatBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Entity\BaseChatMessage;
use WechatWorkAppChatBundle\Entity\MarkdownMessage;

/**
 * @internal
 */
#[CoversClass(MarkdownMessage::class)]
final class MarkdownMessageTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new MarkdownMessage();
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function propertiesProvider(): array
    {
        return [
            'content' => ['content', 'test_value'],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateMarkdownMessage(): void
    {
        $agent = $this->createMock(AgentInterface::class);

        $appChat = new AppChat();
        $appChat->setAgent($agent);
        $appChat->setChatId('test_chat');
        $appChat->setName('测试群聊');
        $appChat->setOwner('owner');

        $markdownMessage = new MarkdownMessage();
        $markdownMessage->setAppChat($appChat);
        $markdownMessage->setContent('# 这是一条Markdown消息\n\n**加粗文本**\n\n- 列表项1\n- 列表项2');
        $markdownMessage->setIsSent(true);
        $markdownMessage->setSentAt(new \DateTimeImmutable());
        $markdownMessage->setMsgId('test_msg_id');

        $this->assertSame($appChat, $markdownMessage->getAppChat());
        $this->assertSame('# 这是一条Markdown消息\n\n**加粗文本**\n\n- 列表项1\n- 列表项2', $markdownMessage->getContent());
        $this->assertTrue($markdownMessage->isSent());
        $this->assertInstanceOf(\DateTimeImmutable::class, $markdownMessage->getSentAt());
        $this->assertSame('test_msg_id', $markdownMessage->getMsgId());
    }

    public function testGetMsgType(): void
    {
        $markdownMessage = new MarkdownMessage();
        $this->assertSame('markdown', $markdownMessage->getMsgType());
    }

    public function testGetRequestContent(): void
    {
        $markdownMessage = new MarkdownMessage();
        $content = '## 标题\n\n这是**加粗**内容';
        $markdownMessage->setContent($content);

        $expected = [
            'markdown' => [
                'content' => $content,
            ],
        ];

        $this->assertSame($expected, $markdownMessage->getRequestContent());
    }

    public function testMarkdownMessageDefaults(): void
    {
        $markdownMessage = new MarkdownMessage();

        $this->assertFalse($markdownMessage->isSent());
        $this->assertNull($markdownMessage->getSentAt());
        $this->assertNull($markdownMessage->getMsgId());
        $this->assertFalse($markdownMessage->isRecalled());
        $this->assertNull($markdownMessage->getRecalledAt());
    }

    public function testSettersWorkCorrectly(): void
    {
        $agent = $this->createMock(AgentInterface::class);

        $appChat = new AppChat();
        $appChat->setAgent($agent);
        $appChat->setChatId('test_chat');
        $appChat->setName('测试群聊');
        $appChat->setOwner('owner');

        $now = new \DateTimeImmutable();
        $markdownMessage = new MarkdownMessage();

        $markdownMessage->setAppChat($appChat);
        $markdownMessage->setContent('# 测试Markdown');
        $markdownMessage->setIsSent(true);
        $markdownMessage->setSentAt($now);
        $markdownMessage->setMsgId('msg123');
        $markdownMessage->setIsRecalled(false);
        $markdownMessage->setRecalledAt(null);

        $this->assertSame($appChat, $markdownMessage->getAppChat());
        $this->assertSame('# 测试Markdown', $markdownMessage->getContent());
        $this->assertTrue($markdownMessage->isSent());
        $this->assertSame($now, $markdownMessage->getSentAt());
        $this->assertSame('msg123', $markdownMessage->getMsgId());
        $this->assertFalse($markdownMessage->isRecalled());
        $this->assertNull($markdownMessage->getRecalledAt());
    }

    public function testStringable(): void
    {
        $markdownMessage = new MarkdownMessage();
        $this->assertIsString((string) $markdownMessage);
    }

    public function testContentValidation(): void
    {
        $markdownMessage = new MarkdownMessage();

        // Test short content
        $shortContent = '# 短内容';
        $markdownMessage->setContent($shortContent);
        $this->assertSame($shortContent, $markdownMessage->getContent());

        // Test long content (up to 4096 characters)
        $longContent = str_repeat('这是测试内容', 200); // Should be around 2400 chars
        $markdownMessage->setContent($longContent);
        $this->assertSame($longContent, $markdownMessage->getContent());
    }

    public function testRecallFunctionality(): void
    {
        $markdownMessage = new MarkdownMessage();
        $recallTime = new \DateTimeImmutable();

        $markdownMessage->setIsRecalled(true);
        $markdownMessage->setRecalledAt($recallTime);

        $this->assertTrue($markdownMessage->isRecalled());
        $this->assertSame($recallTime, $markdownMessage->getRecalledAt());
    }

    public function testMarkdownMessageInheritance(): void
    {
        $markdownMessage = new MarkdownMessage();
        $this->assertInstanceOf(BaseChatMessage::class, $markdownMessage);
    }

    public function testComplexMarkdownContent(): void
    {
        $markdownMessage = new MarkdownMessage();

        $complexContent = <<<'MARKDOWN'
            # 项目报告
            ## 进度
            - [x] 需求分析完成
            - [x] 设计完成
            - [ ] 开发进行中

            ## 代码示例
            ```php
            $message = new MarkdownMessage();
            $message->setContent('Hello World');
            ```

            **注意**：这是*重要*信息
            MARKDOWN;

        $markdownMessage->setContent($complexContent);

        $requestContent = $markdownMessage->getRequestContent();
        $this->assertIsArray($requestContent);
        $this->assertArrayHasKey('markdown', $requestContent);
        $markdownData = $requestContent['markdown'];
        $this->assertIsArray($markdownData);
        $this->assertArrayHasKey('content', $markdownData);
        $this->assertSame($complexContent, $markdownData['content']);
    }

    public function testEmptyContent(): void
    {
        $markdownMessage = new MarkdownMessage();
        $markdownMessage->setContent('');

        $expected = [
            'markdown' => [
                'content' => '',
            ],
        ];

        $this->assertSame($expected, $markdownMessage->getRequestContent());
    }
}
