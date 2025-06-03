<?php

namespace WechatWorkAppChatBundle\Tests\Entity;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Entity\MarkdownMessage;

class MarkdownMessageTest extends TestCase
{
    private MarkdownMessage $markdownMessage;
    private MockObject $appChat;

    protected function setUp(): void
    {
        $this->markdownMessage = new MarkdownMessage();
        $this->appChat = $this->createMock(AppChat::class);
        $this->appChat->expects($this->any())->method('getChatId')->willReturn('test_chat_id');
    }

    public function test_getMsgType_returnsMarkdown(): void
    {
        $this->assertEquals('markdown', $this->markdownMessage->getMsgType());
    }

    public function test_getRequestContent_withValidMarkdown(): void
    {
        $content = '# Markdown Title\n\n**Bold text** and *italic text*';
        $this->markdownMessage->setContent($content);

        $expected = [
            'markdown' => [
                'content' => $content,
            ],
        ];

        $this->assertEquals($expected, $this->markdownMessage->getRequestContent());
    }

    public function test_getRequestContent_withEmptyContent(): void
    {
        $this->markdownMessage->setContent('');

        $expected = [
            'markdown' => [
                'content' => '',
            ],
        ];

        $this->assertEquals($expected, $this->markdownMessage->getRequestContent());
    }

    public function test_getRequestContent_withCodeBlock(): void
    {
        $content = "```php\n<?php\necho 'Hello World';\n```";
        $this->markdownMessage->setContent($content);

        $expected = [
            'markdown' => [
                'content' => $content,
            ],
        ];

        $this->assertEquals($expected, $this->markdownMessage->getRequestContent());
    }

    public function test_setContent_andGetContent(): void
    {
        $content = '## Test markdown content';
        $this->markdownMessage->setContent($content);

        $this->assertEquals($content, $this->markdownMessage->getContent());
    }

    public function test_setContent_withLinks(): void
    {
        $content = '[Link Text](https://example.com)';
        $this->markdownMessage->setContent($content);

        $this->assertEquals($content, $this->markdownMessage->getContent());
    }

    public function test_setContent_withTables(): void
    {
        $content = "| Name | Age |\n|------|-----|\n| John | 25 |";
        $this->markdownMessage->setContent($content);

        $this->assertEquals($content, $this->markdownMessage->getContent());
    }

    public function test_inheritanceFromBaseChatMessage(): void
    {
        /** @var AppChat $appChat */
        $appChat = $this->appChat;
        $this->markdownMessage->setAppChat($appChat);
        $this->markdownMessage->setIsSent(false);
        $this->markdownMessage->setMsgId(null);

        $this->assertEquals($appChat, $this->markdownMessage->getAppChat());
        $this->assertFalse($this->markdownMessage->isSent());
        $this->assertNull($this->markdownMessage->getMsgId());
    }
} 