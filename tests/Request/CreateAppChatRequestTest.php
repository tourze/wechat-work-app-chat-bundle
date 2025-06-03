<?php

namespace WechatWorkAppChatBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use WechatWorkAppChatBundle\Request\CreateAppChatRequest;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;

class CreateAppChatRequestTest extends TestCase
{
    private CreateAppChatRequest $request;
    private Agent $agent;

    protected function setUp(): void
    {
        $this->request = new CreateAppChatRequest();
        
        $corp = new Corp();
        $this->agent = new Agent();
        $this->agent->setCorp($corp);
        $this->agent->setAgentId('test_agent_id');
    }

    public function test_getRequestPath(): void
    {
        $this->assertEquals('/cgi-bin/appchat/create', $this->request->getRequestPath());
    }

    public function test_setName_andGetName(): void
    {
        $name = '测试群聊';
        $this->request->setName($name);

        $this->assertEquals($name, $this->request->getName());
    }

    public function test_setOwner_andGetOwner(): void
    {
        $owner = 'test_owner_user';
        $this->request->setOwner($owner);

        $this->assertEquals($owner, $this->request->getOwner());
    }

    public function test_setUserList_andGetUserList(): void
    {
        $userList = ['user1', 'user2', 'user3'];
        $this->request->setUserList($userList);

        $this->assertEquals($userList, $this->request->getUserList());
    }

    public function test_setUserList_withEmptyArray(): void
    {
        $this->request->setUserList([]);

        $this->assertEquals([], $this->request->getUserList());
    }

    public function test_getRequestOptions_withAllFields(): void
    {
        $name = '项目讨论群';
        $owner = 'project_owner';
        $userList = ['user1', 'user2', 'user3'];

        $this->request->setAgent($this->agent);
        $this->request->setName($name);
        $this->request->setOwner($owner);
        $this->request->setUserList($userList);

        $expected = [
            'json' => [
                'name' => $name,
                'owner' => $owner,
                'userlist' => $userList,
            ],
        ];

        $this->assertEquals($expected, $this->request->getRequestOptions());
    }

    public function test_getRequestOptions_withMinimalFields(): void
    {
        $name = 'Simple Chat';
        $owner = 'simple_owner';
        $userList = ['single_user'];

        $this->request->setAgent($this->agent);
        $this->request->setName($name);
        $this->request->setOwner($owner);
        $this->request->setUserList($userList);

        $expected = [
            'json' => [
                'name' => $name,
                'owner' => $owner,
                'userlist' => $userList,
            ],
        ];

        $this->assertEquals($expected, $this->request->getRequestOptions());
    }

    public function test_getRequestOptions_withSpecialCharacters(): void
    {
        $name = '特殊字符群聊 @#$%^&*()';
        $owner = 'owner_with-special.chars_123';
        $userList = ['user-1', 'user_2', 'user.3'];

        $this->request->setAgent($this->agent);
        $this->request->setName($name);
        $this->request->setOwner($owner);
        $this->request->setUserList($userList);

        $expected = [
            'json' => [
                'name' => $name,
                'owner' => $owner,
                'userlist' => $userList,
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
        $this->request->setAgent($this->agent);
        $this->request->setName('Chain Test');
        $this->request->setOwner('chain_owner');
        $this->request->setUserList(['chain_user']);

        $this->assertEquals($this->agent, $this->request->getAgent());
        $this->assertEquals('Chain Test', $this->request->getName());
        $this->assertEquals('chain_owner', $this->request->getOwner());
        $this->assertEquals(['chain_user'], $this->request->getUserList());
    }
} 