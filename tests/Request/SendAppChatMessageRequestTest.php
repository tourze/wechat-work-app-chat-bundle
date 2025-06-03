<?php

namespace WechatWorkAppChatBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Entity\BaseChatMessage;
use WechatWorkAppChatBundle\Entity\TextMessage;
use WechatWorkAppChatBundle\Request\SendAppChatMessageRequest;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;

class SendAppChatMessageRequestTest extends TestCase
{
    private SendAppChatMessageRequest $request;
    private Agent $agent;
    private AppChat $appChat;

    protected function setUp(): void
    {
        $this->request = new SendAppChatMessageRequest();
        
        $corp = new Corp();
        $this->agent = new Agent();
        $this->agent->setCorp($corp);
        $this->agent->setAgentId('test_agent_id');
        
        $this->appChat = new AppChat();
        $this->appChat->setAgent($this->agent);
        $this->appChat->setChatId('test_chat_id');
    }

    public function test_getRequestPath(): void
    {
        $this->assertEquals('/cgi-bin/appchat/send', $this->request->getRequestPath());
    }

    public function test_setMessage_andGetMessage(): void
    {
        $message = new TextMessage();
        $message->setAppChat($this->appChat);
        $message->setContent('Test message');

        $this->request->setMessage($message);

        $this->assertEquals($message, $this->request->getMessage());
    }

    public function test_setMessage_setsAgentAutomatically(): void
    {
        $message = new TextMessage();
        $message->setAppChat($this->appChat);
        $message->setContent('Test message');

        $this->request->setMessage($message);

        $this->assertEquals($this->agent, $this->request->getAgent());
    }

    public function test_getRequestOptions_withTextMessage(): void
    {
        $message = new TextMessage();
        $message->setAppChat($this->appChat);
        $message->setContent('Hello World');

        $this->request->setMessage($message);

        $expected = [
            'json' => [
                'chatid' => 'test_chat_id',
                'msgtype' => 'text',
                'text' => [
                    'content' => 'Hello World',
                ],
            ],
        ];

        $this->assertEquals($expected, $this->request->getRequestOptions());
    }

    public function test_getRequestOptions_withEmptyContent(): void
    {
        $message = new TextMessage();
        $message->setAppChat($this->appChat);
        $message->setContent('');

        $this->request->setMessage($message);

        $expected = [
            'json' => [
                'chatid' => 'test_chat_id',
                'msgtype' => 'text',
                'text' => [
                    'content' => '',
                ],
            ],
        ];

        $this->assertEquals($expected, $this->request->getRequestOptions());
    }

    public function test_getRequestOptions_withSpecialCharacters(): void
    {
        $message = new TextMessage();
        $message->setAppChat($this->appChat);
        $message->setContent('特殊字符测试 @#$%^&*()');

        $this->request->setMessage($message);

        $expected = [
            'json' => [
                'chatid' => 'test_chat_id',
                'msgtype' => 'text',
                'text' => [
                    'content' => '特殊字符测试 @#$%^&*()',
                ],
            ],
        ];

        $this->assertEquals($expected, $this->request->getRequestOptions());
    }

    public function test_getRequestOptions_mergesContentCorrectly(): void
    {
        // 创建一个测试用的消息类来验证合并逻辑
        $message = new class extends BaseChatMessage {
            public function getMsgType(): string
            {
                return 'custom';
            }

            public function getRequestContent(): array
            {
                return [
                    'custom' => [
                        'field1' => 'value1',
                        'field2' => 'value2',
                    ],
                    'extra' => 'data',
                ];
            }
        };

        $message->setAppChat($this->appChat);

        $this->request->setMessage($message);

        $expected = [
            'json' => [
                'chatid' => 'test_chat_id',
                'msgtype' => 'custom',
                'custom' => [
                    'field1' => 'value1',
                    'field2' => 'value2',
                ],
                'extra' => 'data',
            ],
        ];

        $this->assertEquals($expected, $this->request->getRequestOptions());
    }

    public function test_agentAware_trait(): void
    {
        $this->request->setAgent($this->agent);

        $this->assertEquals($this->agent, $this->request->getAgent());
    }
} 