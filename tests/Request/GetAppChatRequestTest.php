<?php

namespace WechatWorkAppChatBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use WechatWorkAppChatBundle\Request\GetAppChatRequest;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;

class GetAppChatRequestTest extends TestCase
{
    private GetAppChatRequest $request;
    private Agent $agent;

    protected function setUp(): void
    {
        $this->request = new GetAppChatRequest();
        
        $corp = new Corp();
        $this->agent = new Agent();
        $this->agent->setCorp($corp);
        $this->agent->setAgentId('test_agent_id');
    }

    public function test_getRequestPath(): void
    {
        $this->assertEquals('/cgi-bin/appchat/get', $this->request->getRequestPath());
    }

    public function test_setChatId_andGetChatId(): void
    {
        $chatId = 'test_chat_123456';
        $this->request->setChatId($chatId);

        $this->assertEquals($chatId, $this->request->getChatId());
    }

    public function test_setChatId_withSpecialCharacters(): void
    {
        $chatId = 'chat-id_with.special_chars123';
        $this->request->setChatId($chatId);

        $this->assertEquals($chatId, $this->request->getChatId());
    }

    public function test_getRequestOptions_withValidChatId(): void
    {
        $chatId = 'valid_chat_id_789';

        $this->request->setAgent($this->agent);
        $this->request->setChatId($chatId);

        $expected = [
            'query' => [
                'chatid' => $chatId,
            ],
        ];

        $this->assertEquals($expected, $this->request->getRequestOptions());
    }

    public function test_getRequestOptions_withEmptyChatId(): void
    {
        $chatId = '';

        $this->request->setAgent($this->agent);
        $this->request->setChatId($chatId);

        $expected = [
            'query' => [
                'chatid' => $chatId,
            ],
        ];

        $this->assertEquals($expected, $this->request->getRequestOptions());
    }

    public function test_getRequestOptions_withLongChatId(): void
    {
        $chatId = str_repeat('a', 32);

        $this->request->setAgent($this->agent);
        $this->request->setChatId($chatId);

        $expected = [
            'query' => [
                'chatid' => $chatId,
            ],
        ];

        $this->assertEquals($expected, $this->request->getRequestOptions());
    }

    public function test_agentAware_trait(): void
    {
        $this->request->setAgent($this->agent);

        $this->assertEquals($this->agent, $this->request->getAgent());
    }

    public function test_chaining_methods(): void
    {
        $chatId = 'chain_test_chat';

        $this->request->setAgent($this->agent);
        $this->request->setChatId($chatId);

        $this->assertEquals($this->agent, $this->request->getAgent());
        $this->assertEquals($chatId, $this->request->getChatId());
    }
} 