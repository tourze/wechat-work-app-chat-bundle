<?php

namespace WechatWorkAppChatBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use WechatWorkAppChatBundle\Request\UpdateAppChatRequest;
use WechatWorkBundle\Entity\Agent;

class UpdateAppChatRequestTest extends TestCase
{
    private UpdateAppChatRequest $request;
    private Agent $agent;

    protected function setUp(): void
    {
        $this->request = new UpdateAppChatRequest();
        $this->agent = $this->createMock(Agent::class);
    }

    public function testGetRequestPath(): void
    {
        $this->assertEquals('/cgi-bin/appchat/update', $this->request->getRequestPath());
    }

    public function testSetAndGetChatId(): void
    {
        $chatId = 'test_chat_id';
        $this->request->setChatId($chatId);
        $this->assertEquals($chatId, $this->request->getChatId());
    }

    public function testSetAndGetName(): void
    {
        $name = 'Test Chat Name';
        $this->request->setName($name);
        $this->assertEquals($name, $this->request->getName());
    }

    public function testSetAndGetOwner(): void
    {
        $owner = 'test_owner_userid';
        $this->request->setOwner($owner);
        $this->assertEquals($owner, $this->request->getOwner());
    }

    public function testSetAndGetAddUserList(): void
    {
        $addUserList = ['user1', 'user2', 'user3'];
        $this->request->setAddUserList($addUserList);
        $this->assertEquals($addUserList, $this->request->getAddUserList());
    }

    public function testSetAndGetDelUserList(): void
    {
        $delUserList = ['user4', 'user5'];
        $this->request->setDelUserList($delUserList);
        $this->assertEquals($delUserList, $this->request->getDelUserList());
    }

    public function testSetAndGetAgent(): void
    {
        $this->request->setAgent($this->agent);
        $this->assertSame($this->agent, $this->request->getAgent());
    }

    public function testGetRequestOptions_withAllFields(): void
    {
        $chatId = 'test_chat_id';
        $name = 'Test Chat Name';
        $owner = 'test_owner_userid';
        $addUserList = ['user1', 'user2', 'user3'];
        $delUserList = ['user4', 'user5'];
        
        $this->request->setChatId($chatId);
        $this->request->setName($name);
        $this->request->setOwner($owner);
        $this->request->setAddUserList($addUserList);
        $this->request->setDelUserList($delUserList);
        
        $expectedOptions = [
            'json' => [
                'chatid' => $chatId,
                'name' => $name,
                'owner' => $owner,
                'add_user_list' => $addUserList,
                'del_user_list' => $delUserList,
            ],
        ];
        
        $this->assertEquals($expectedOptions, $this->request->getRequestOptions());
    }
} 