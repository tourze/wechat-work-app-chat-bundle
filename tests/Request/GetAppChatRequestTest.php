<?php

namespace WechatWorkAppChatBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use WechatWorkAppChatBundle\Request\GetAppChatRequest;
use WechatWorkBundle\Entity\Agent;

class GetAppChatRequestTest extends TestCase
{
    private GetAppChatRequest $request;
    private Agent $agent;

    protected function setUp(): void
    {
        $this->request = new GetAppChatRequest();
        $this->agent = $this->createMock(Agent::class);
    }

    public function testGetRequestPath(): void
    {
        $this->assertEquals('/cgi-bin/appchat/get', $this->request->getRequestPath());
    }

    public function testSetAndGetChatId(): void
    {
        $chatId = 'test_chat_id';
        $this->request->setChatId($chatId);
        $this->assertEquals($chatId, $this->request->getChatId());
    }

    public function testSetAndGetAgent(): void
    {
        $this->request->setAgent($this->agent);
        $this->assertSame($this->agent, $this->request->getAgent());
    }

    public function testGetRequestOptions(): void
    {
        $chatId = 'test_chat_id';
        $this->request->setChatId($chatId);
        
        $expectedOptions = [
            'query' => [
                'chatid' => $chatId,
            ],
        ];
        
        $this->assertEquals($expectedOptions, $this->request->getRequestOptions());
    }
} 