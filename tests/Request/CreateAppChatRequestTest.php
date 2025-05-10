<?php

namespace WechatWorkAppChatBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use WechatWorkAppChatBundle\Request\CreateAppChatRequest;
use WechatWorkBundle\Entity\Agent;

class CreateAppChatRequestTest extends TestCase
{
    private CreateAppChatRequest $request;
    private Agent $agent;

    protected function setUp(): void
    {
        $this->request = new CreateAppChatRequest();
        $this->agent = $this->createMock(Agent::class);
    }

    public function testGetRequestPath(): void
    {
        $this->assertEquals('/cgi-bin/appchat/create', $this->request->getRequestPath());
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

    public function testSetAndGetUserList(): void
    {
        $userList = ['user1', 'user2', 'user3'];
        $this->request->setUserList($userList);
        $this->assertEquals($userList, $this->request->getUserList());
    }

    public function testSetAndGetAgent(): void
    {
        $this->request->setAgent($this->agent);
        $this->assertSame($this->agent, $this->request->getAgent());
    }

    public function testGetRequestOptions(): void
    {
        $name = 'Test Chat Name';
        $owner = 'test_owner_userid';
        $userList = ['user1', 'user2', 'user3'];
        
        $this->request->setName($name);
        $this->request->setOwner($owner);
        $this->request->setUserList($userList);
        
        $expectedOptions = [
            'json' => [
                'name' => $name,
                'owner' => $owner,
                'userlist' => $userList,
            ],
        ];
        
        $this->assertEquals($expectedOptions, $this->request->getRequestOptions());
    }
} 