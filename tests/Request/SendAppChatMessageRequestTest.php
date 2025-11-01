<?php

namespace WechatWorkAppChatBundle\Tests\Request;

use HttpClientBundle\Test\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Entity\FileMessage;
use WechatWorkAppChatBundle\Entity\ImageMessage;
use WechatWorkAppChatBundle\Entity\MarkdownMessage;
use WechatWorkAppChatBundle\Entity\TextMessage;
use WechatWorkAppChatBundle\Request\SendAppChatMessageRequest;

/**
 * @internal
 */
#[CoversClass(SendAppChatMessageRequest::class)]
final class SendAppChatMessageRequestTest extends RequestTestCase
{
    public function testCreateRequestWithTextMessage(): void
    {
        $agent = $this->createMock(AgentInterface::class);

        $appChat = new AppChat();
        $appChat->setAgent($agent);
        $appChat->setChatId('test_chat_123');
        $appChat->setName('测试群聊');
        $appChat->setOwner('owner');

        $textMessage = new TextMessage();
        $textMessage->setAppChat($appChat);
        $textMessage->setContent('测试文本消息');

        $request = new SendAppChatMessageRequest();
        $request->setMessage($textMessage);

        $this->assertSame($textMessage, $request->getMessage());
        $this->assertSame($agent, $request->getAgent());
    }

    public function testGetRequestPath(): void
    {
        $request = new SendAppChatMessageRequest();
        $this->assertSame('/cgi-bin/appchat/send', $request->getRequestPath());
    }

    public function testGetRequestOptionsWithTextMessage(): void
    {
        $agent = $this->createMock(AgentInterface::class);

        $appChat = new AppChat();
        $appChat->setAgent($agent);
        $appChat->setChatId('test_chat');
        $appChat->setName('测试群聊');
        $appChat->setOwner('owner');

        $textMessage = new TextMessage();
        $textMessage->setAppChat($appChat);
        $textMessage->setContent('测试消息内容');

        $request = new SendAppChatMessageRequest();
        $request->setMessage($textMessage);

        $expected = [
            'json' => [
                'chatid' => 'test_chat',
                'msgtype' => 'text',
                'text' => [
                    'content' => '测试消息内容',
                ],
            ],
        ];

        $this->assertSame($expected, $request->getRequestOptions());
    }

    public function testGetRequestOptionsWithMarkdownMessage(): void
    {
        $agent = $this->createMock(AgentInterface::class);

        $appChat = new AppChat();
        $appChat->setAgent($agent);
        $appChat->setChatId('markdown_chat');
        $appChat->setName('测试群聊');
        $appChat->setOwner('owner');

        $markdownMessage = new MarkdownMessage();
        $markdownMessage->setAppChat($appChat);
        $markdownMessage->setContent('# 标题\n\n这是**加粗**内容');

        $request = new SendAppChatMessageRequest();
        $request->setMessage($markdownMessage);

        $expected = [
            'json' => [
                'chatid' => 'markdown_chat',
                'msgtype' => 'markdown',
                'markdown' => [
                    'content' => '# 标题\n\n这是**加粗**内容',
                ],
            ],
        ];

        $this->assertSame($expected, $request->getRequestOptions());
    }

    public function testGetRequestOptionsWithImageMessage(): void
    {
        $agent = $this->createMock(AgentInterface::class);

        $appChat = new AppChat();
        $appChat->setAgent($agent);
        $appChat->setChatId('image_chat');
        $appChat->setName('测试群聊');
        $appChat->setOwner('owner');

        $imageMessage = new ImageMessage();
        $imageMessage->setAppChat($appChat);
        $imageMessage->setMediaId('image_media_123');

        $request = new SendAppChatMessageRequest();
        $request->setMessage($imageMessage);

        $expected = [
            'json' => [
                'chatid' => 'image_chat',
                'msgtype' => 'image',
                'image' => [
                    'media_id' => 'image_media_123',
                ],
            ],
        ];

        $this->assertSame($expected, $request->getRequestOptions());
    }

    public function testGetRequestOptionsWithFileMessage(): void
    {
        $agent = $this->createMock(AgentInterface::class);

        $appChat = new AppChat();
        $appChat->setAgent($agent);
        $appChat->setChatId('file_chat');
        $appChat->setName('测试群聊');
        $appChat->setOwner('owner');

        $fileMessage = new FileMessage();
        $fileMessage->setAppChat($appChat);
        $fileMessage->setMediaId('file_media_456');

        $request = new SendAppChatMessageRequest();
        $request->setMessage($fileMessage);

        $expected = [
            'json' => [
                'chatid' => 'file_chat',
                'msgtype' => 'file',
                'file' => [
                    'media_id' => 'file_media_456',
                ],
            ],
        ];

        $this->assertSame($expected, $request->getRequestOptions());
    }

    public function testSetMessageAutomaticallySetsAgent(): void
    {
        $agent = $this->createMock(AgentInterface::class);

        $appChat = new AppChat();
        $appChat->setAgent($agent);
        $appChat->setChatId('auto_agent_chat');
        $appChat->setName('测试群聊');
        $appChat->setOwner('owner');

        $textMessage = new TextMessage();
        $textMessage->setAppChat($appChat);
        $textMessage->setContent('自动设置Agent测试');

        $request = new SendAppChatMessageRequest();
        $request->setMessage($textMessage);

        $this->assertSame($agent, $request->getAgent());
    }

    public function testRequestStructure(): void
    {
        $agent = $this->createMock(AgentInterface::class);

        $appChat = new AppChat();
        $appChat->setAgent($agent);
        $appChat->setChatId('structure_test');
        $appChat->setName('测试群聊');
        $appChat->setOwner('owner');

        $textMessage = new TextMessage();
        $textMessage->setAppChat($appChat);
        $textMessage->setContent('结构测试');

        $request = new SendAppChatMessageRequest();
        $request->setMessage($textMessage);

        $options = $request->getRequestOptions();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);

        $this->assertIsArray($options['json']);
        $json = $options['json'];
        $this->assertArrayHasKey('chatid', $json);
        $this->assertArrayHasKey('msgtype', $json);
        $this->assertIsString($json['chatid']);
        $this->assertIsString($json['msgtype']);
    }

    public function testComplexMarkdownContent(): void
    {
        $agent = $this->createMock(AgentInterface::class);

        $appChat = new AppChat();
        $appChat->setAgent($agent);
        $appChat->setChatId('complex_markdown');
        $appChat->setName('测试群聊');
        $appChat->setOwner('owner');

        $complexMarkdown = <<<'MARKDOWN'
            # 项目报告
            ## 进度
            - [x] 需求分析完成
            - [x] 设计完成
            - [ ] 开发进行中

            **注意**：这是*重要*信息
            MARKDOWN;

        $markdownMessage = new MarkdownMessage();
        $markdownMessage->setAppChat($appChat);
        $markdownMessage->setContent($complexMarkdown);

        $request = new SendAppChatMessageRequest();
        $request->setMessage($markdownMessage);

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        /** @var array<string, mixed> $json */
        $json = $options['json'];
        $this->assertArrayHasKey('markdown', $json);
        $this->assertIsArray($json['markdown']);
        $this->assertArrayHasKey('content', $json['markdown']);
        $this->assertSame($complexMarkdown, $json['markdown']['content']);
    }

    public function testMessageContentMerging(): void
    {
        $agent = $this->createMock(AgentInterface::class);

        $appChat = new AppChat();
        $appChat->setAgent($agent);
        $appChat->setChatId('merge_test');
        $appChat->setName('测试群聊');
        $appChat->setOwner('owner');

        $textMessage = new TextMessage();
        $textMessage->setAppChat($appChat);
        $textMessage->setContent('合并测试');

        $request = new SendAppChatMessageRequest();
        $request->setMessage($textMessage);

        $options = $request->getRequestOptions();

        // Verify that message content is merged correctly
        $expectedBase = [
            'chatid' => 'merge_test',
            'msgtype' => 'text',
        ];

        $messageContent = $textMessage->getRequestContent();
        $expected = array_merge($expectedBase, $messageContent);

        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertSame($expected, $options['json']);
    }
}
