<?php

namespace WechatWorkAppChatBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use WechatWorkAppChatBundle\Request\UpdateAppChatRequest;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;

class UpdateAppChatRequestTest extends TestCase
{
    private UpdateAppChatRequest $request;
    private Agent $agent;

    protected function setUp(): void
    {
        $this->request = new UpdateAppChatRequest();
        
        $corp = new Corp();
        $this->agent = new Agent();
        $this->agent->setCorp($corp);
        $this->agent->setAgentId('test_agent_id');
    }

    public function test_getRequestPath(): void
    {
        $this->assertEquals('/cgi-bin/appchat/update', $this->request->getRequestPath());
    }

    public function test_setChatId_andGetChatId(): void
    {
        $chatId = 'test_chat_update_123';
        $this->request->setChatId($chatId);

        $this->assertEquals($chatId, $this->request->getChatId());
    }

    public function test_setName_andGetName(): void
    {
        $name = '更新后的群聊名称';
        $this->request->setName($name);

        $this->assertEquals($name, $this->request->getName());
    }

    public function test_setOwner_andGetOwner(): void
    {
        $owner = 'new_owner_user';
        $this->request->setOwner($owner);

        $this->assertEquals($owner, $this->request->getOwner());
    }

    public function test_setAddUserList_andGetAddUserList(): void
    {
        $addUserList = ['new_user1', 'new_user2'];
        $this->request->setAddUserList($addUserList);

        $this->assertEquals($addUserList, $this->request->getAddUserList());
    }

    public function test_setAddUserList_withEmptyArray(): void
    {
        $this->request->setAddUserList([]);

        $this->assertEquals([], $this->request->getAddUserList());
    }

    public function test_setDelUserList_andGetDelUserList(): void
    {
        $delUserList = ['remove_user1', 'remove_user2'];
        $this->request->setDelUserList($delUserList);

        $this->assertEquals($delUserList, $this->request->getDelUserList());
    }

    public function test_setDelUserList_withEmptyArray(): void
    {
        $this->request->setDelUserList([]);

        $this->assertEquals([], $this->request->getDelUserList());
    }

    public function test_getRequestOptions_withAllFields(): void
    {
        $chatId = 'update_chat_id';
        $name = '完整更新群聊';
        $owner = 'update_owner';
        $addUserList = ['add_user1', 'add_user2'];
        $delUserList = ['del_user1'];

        $this->request->setAgent($this->agent);
        $this->request->setChatId($chatId);
        $this->request->setName($name);
        $this->request->setOwner($owner);
        $this->request->setAddUserList($addUserList);
        $this->request->setDelUserList($delUserList);

        $expected = [
            'json' => [
                'chatid' => $chatId,
                'name' => $name,
                'owner' => $owner,
                'add_user_list' => $addUserList,
                'del_user_list' => $delUserList,
            ],
        ];

        $this->assertEquals($expected, $this->request->getRequestOptions());
    }

    public function test_getRequestOptions_withMinimalFields(): void
    {
        $chatId = 'minimal_update_chat';
        $name = 'Minimal Update';
        $owner = 'minimal_owner';

        $this->request->setAgent($this->agent);
        $this->request->setChatId($chatId);
        $this->request->setName($name);
        $this->request->setOwner($owner);
        // 默认值为空数组

        $expected = [
            'json' => [
                'chatid' => $chatId,
                'name' => $name,
                'owner' => $owner,
                'add_user_list' => [],
                'del_user_list' => [],
            ],
        ];

        $this->assertEquals($expected, $this->request->getRequestOptions());
    }

    public function test_getRequestOptions_withOnlyAddUsers(): void
    {
        $chatId = 'add_only_chat';
        $name = 'Add Users Only';
        $owner = 'add_owner';
        $addUserList = ['new_user1', 'new_user2', 'new_user3'];

        $this->request->setAgent($this->agent);
        $this->request->setChatId($chatId);
        $this->request->setName($name);
        $this->request->setOwner($owner);
        $this->request->setAddUserList($addUserList);

        $expected = [
            'json' => [
                'chatid' => $chatId,
                'name' => $name,
                'owner' => $owner,
                'add_user_list' => $addUserList,
                'del_user_list' => [],
            ],
        ];

        $this->assertEquals($expected, $this->request->getRequestOptions());
    }

    public function test_getRequestOptions_withOnlyDeleteUsers(): void
    {
        $chatId = 'del_only_chat';
        $name = 'Delete Users Only';
        $owner = 'del_owner';
        $delUserList = ['old_user1', 'old_user2'];

        $this->request->setAgent($this->agent);
        $this->request->setChatId($chatId);
        $this->request->setName($name);
        $this->request->setOwner($owner);
        $this->request->setDelUserList($delUserList);

        $expected = [
            'json' => [
                'chatid' => $chatId,
                'name' => $name,
                'owner' => $owner,
                'add_user_list' => [],
                'del_user_list' => $delUserList,
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