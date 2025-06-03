<?php

namespace WechatWorkAppChatBundle\Tests\Entity;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Entity\TextMessage;

class TextMessageTest extends TestCase
{
    private TextMessage $textMessage;
    private MockObject $appChat;

    protected function setUp(): void
    {
        $this->textMessage = new TextMessage();
        $this->appChat = $this->createMock(AppChat::class);
        $this->appChat->expects($this->any())->method('getChatId')->willReturn('test_chat_id');
    }

    public function test_getMsgType_returnsText(): void
    {
        $this->assertEquals('text', $this->textMessage->getMsgType());
    }

    public function test_getRequestContent_withValidContent(): void
    {
        $content = 'Test message content';
        $this->textMessage->setContent($content);

        $expected = [
            'text' => [
                'content' => $content,
            ],
        ];

        $this->assertEquals($expected, $this->textMessage->getRequestContent());
    }

    public function test_getRequestContent_withEmptyContent(): void
    {
        $this->textMessage->setContent('');

        $expected = [
            'text' => [
                'content' => '',
            ],
        ];

        $this->assertEquals($expected, $this->textMessage->getRequestContent());
    }

    public function test_getRequestContent_withSpecialCharacters(): void
    {
        $content = '特殊字符测试: @#$%^&*()_+{}|:"<>?';
        $this->textMessage->setContent($content);

        $expected = [
            'text' => [
                'content' => $content,
            ],
        ];

        $this->assertEquals($expected, $this->textMessage->getRequestContent());
    }

    public function test_setContent_andGetContent(): void
    {
        $content = 'Test content';
        $this->textMessage->setContent($content);

        $this->assertEquals($content, $this->textMessage->getContent());
    }

    public function test_setContent_withLongText(): void
    {
        $content = str_repeat('A', 10000);
        $this->textMessage->setContent($content);

        $this->assertEquals($content, $this->textMessage->getContent());
    }

    public function test_setContent_withUnicodeCharacters(): void
    {
        $content = '测试Unicode字符 🎉 emoji 测试';
        $this->textMessage->setContent($content);

        $this->assertEquals($content, $this->textMessage->getContent());
    }

    public function test_inheritanceFromBaseChatMessage(): void
    {
        /** @var AppChat $appChat */
        $appChat = $this->appChat;
        $this->textMessage->setAppChat($appChat);
        $this->textMessage->setIsSent(true);
        $this->textMessage->setMsgId('test_msg_id');

        $this->assertEquals($appChat, $this->textMessage->getAppChat());
        $this->assertTrue($this->textMessage->isSent());
        $this->assertEquals('test_msg_id', $this->textMessage->getMsgId());
    }
} 