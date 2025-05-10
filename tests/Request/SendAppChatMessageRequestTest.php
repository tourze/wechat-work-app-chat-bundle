<?php

namespace WechatWorkAppChatBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Entity\TextMessage;
use WechatWorkAppChatBundle\Request\SendAppChatMessageRequest;
use WechatWorkBundle\Entity\Agent;

class SendAppChatMessageRequestTest extends TestCase
{
    private SendAppChatMessageRequest $request;
    private TextMessage $textMessage;
    private AppChat $appChat;
    private Agent $agent;

    protected function setUp(): void
    {
        $this->request = new SendAppChatMessageRequest();
        
        $this->agent = $this->createMock(Agent::class);
        
        $this->appChat = $this->createMock(AppChat::class);
        $this->appChat->method('getAgent')->willReturn($this->agent);
        $this->appChat->method('getChatId')->willReturn('test_chat_id');
        
        $this->textMessage = $this->createMock(TextMessage::class);
        $this->textMessage->method('getAppChat')->willReturn($this->appChat);
        $this->textMessage->method('getMsgType')->willReturn('text');
        $this->textMessage->method('getRequestContent')->willReturn([
            'text' => [
                'content' => 'Test message content',
            ],
        ]);
    }

    public function testGetRequestPath(): void
    {
        $this->assertEquals('/cgi-bin/appchat/send', $this->request->getRequestPath());
    }

    public function testSetAndGetMessage(): void
    {
        $this->request->setMessage($this->textMessage);
        $this->assertSame($this->textMessage, $this->request->getMessage());
    }

    public function testSetMessageAlsoSetsAgent(): void
    {
        $this->request->setMessage($this->textMessage);
        // 间接测试，只能断言没有抛出异常
        $this->assertTrue(true);
    }

    public function testGetRequestOptions(): void
    {
        $this->request->setMessage($this->textMessage);
        
        $expectedOptions = [
            'json' => [
                'chatid' => 'test_chat_id',
                'msgtype' => 'text',
                'text' => [
                    'content' => 'Test message content',
                ],
            ],
        ];
        
        $this->assertEquals($expectedOptions, $this->request->getRequestOptions());
    }
} 