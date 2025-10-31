<?php

namespace WechatWorkAppChatBundle\Tests\Request;

use HttpClientBundle\Tests\Request\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkAppChatBundle\Request\UpdateAppChatRequest;

/**
 * @internal
 */
#[CoversClass(UpdateAppChatRequest::class)]
final class UpdateAppChatRequestTest extends RequestTestCase
{
    public function testCreateRequest(): void
    {
        $agent = $this->createMock(AgentInterface::class);

        $request = new UpdateAppChatRequest();
        $request->setAgent($agent);
        $request->setChatId('test_chat_123');
        $request->setName('更新群聊');
        $request->setOwner('new_owner');
        $request->setAddUserList(['user4', 'user5']);
        $request->setDelUserList(['user1', 'user2']);

        $this->assertSame($agent, $request->getAgent());
        $this->assertSame('test_chat_123', $request->getChatId());
        $this->assertSame('更新群聊', $request->getName());
        $this->assertSame('new_owner', $request->getOwner());
        $this->assertSame(['user4', 'user5'], $request->getAddUserList());
        $this->assertSame(['user1', 'user2'], $request->getDelUserList());
    }

    public function testGetRequestPath(): void
    {
        $request = new UpdateAppChatRequest();
        $this->assertSame('/cgi-bin/appchat/update', $request->getRequestPath());
    }

    public function testGetRequestOptions(): void
    {
        $request = new UpdateAppChatRequest();
        $request->setChatId('update_chat');
        $request->setName('测试更新');
        $request->setOwner('owner_update');
        $request->setAddUserList(['add1', 'add2']);
        $request->setDelUserList(['del1']);

        $expected = [
            'json' => [
                'chatid' => 'update_chat',
                'name' => '测试更新',
                'owner' => 'owner_update',
                'add_user_list' => ['add1', 'add2'],
                'del_user_list' => ['del1'],
            ],
        ];

        $this->assertSame($expected, $request->getRequestOptions());
    }

    public function testEmptyUserLists(): void
    {
        $request = new UpdateAppChatRequest();
        $request->setChatId('empty_lists_chat');
        $request->setName('Empty Lists');
        $request->setOwner('owner');
        $request->setAddUserList([]);
        $request->setDelUserList([]);

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertSame([], $options['json']['add_user_list']);
        $this->assertSame([], $options['json']['del_user_list']);
    }

    public function testDefaultUserLists(): void
    {
        $request = new UpdateAppChatRequest();
        $this->assertSame([], $request->getAddUserList());
        $this->assertSame([], $request->getDelUserList());
    }

    public function testSingleUserInLists(): void
    {
        $request = new UpdateAppChatRequest();
        $request->setChatId('single_user_chat');
        $request->setName('Single User');
        $request->setOwner('owner');
        $request->setAddUserList(['single_add']);
        $request->setDelUserList(['single_del']);

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertSame(['single_add'], $options['json']['add_user_list']);
        $this->assertSame(['single_del'], $options['json']['del_user_list']);
    }

    public function testMultipleUsersInLists(): void
    {
        $request = new UpdateAppChatRequest();
        $request->setChatId('multiple_users_chat');
        $request->setName('Multiple Users');
        $request->setOwner('owner');

        $addUsers = ['add1', 'add2', 'add3', 'add4'];
        $delUsers = ['del1', 'del2', 'del3'];

        $request->setAddUserList($addUsers);
        $request->setDelUserList($delUsers);

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertSame($addUsers, $options['json']['add_user_list']);
        $this->assertSame($delUsers, $options['json']['del_user_list']);
        $this->assertCount(4, $options['json']['add_user_list']);
        $this->assertCount(3, $options['json']['del_user_list']);
    }

    public function testChineseCharactersInName(): void
    {
        $request = new UpdateAppChatRequest();
        $request->setChatId('chinese_name_chat');
        $request->setName('中文群聊名称测试更新');
        $request->setOwner('owner');
        $request->setAddUserList([]);
        $request->setDelUserList([]);

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertSame('中文群聊名称测试更新', $options['json']['name']);
    }

    public function testSpecialCharactersInOwner(): void
    {
        $request = new UpdateAppChatRequest();
        $request->setChatId('special_owner_chat');
        $request->setName('Special Owner');
        $request->setOwner('owner_with-special.chars_456');
        $request->setAddUserList([]);
        $request->setDelUserList([]);

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertSame('owner_with-special.chars_456', $options['json']['owner']);
    }

    public function testLongUserLists(): void
    {
        $request = new UpdateAppChatRequest();
        $request->setChatId('long_lists_chat');
        $request->setName('Long Lists');
        $request->setOwner('owner');

        // Create long lists
        $addUsers = [];
        $delUsers = [];
        for ($i = 1; $i <= 20; ++$i) {
            $addUsers[] = "add_user{$i}";
        }
        for ($i = 1; $i <= 15; ++$i) {
            $delUsers[] = "del_user{$i}";
        }

        $request->setAddUserList($addUsers);
        $request->setDelUserList($delUsers);

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        /** @var array<string, mixed> $json */
        $json = $options['json'];
        $this->assertArrayHasKey('add_user_list', $json);
        $this->assertArrayHasKey('del_user_list', $json);
        $this->assertIsArray($json['add_user_list']);
        $this->assertIsArray($json['del_user_list']);
        $this->assertCount(20, $json['add_user_list']);
        $this->assertCount(15, $json['del_user_list']);
        $this->assertSame('add_user1', $json['add_user_list'][0]);
        $this->assertSame('add_user20', $json['add_user_list'][19]);
        $this->assertSame('del_user1', $json['del_user_list'][0]);
        $this->assertSame('del_user15', $json['del_user_list'][14]);
    }

    public function testRequestStructure(): void
    {
        $request = new UpdateAppChatRequest();
        $request->setChatId('structure_test');
        $request->setName('Structure Test');
        $request->setOwner('owner');
        $request->setAddUserList(['add1']);
        $request->setDelUserList(['del1']);

        $options = $request->getRequestOptions();

        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);

        /** @var array<string, mixed> $json */
        $json = $options['json'];
        $this->assertArrayHasKey('chatid', $json);
        $this->assertArrayHasKey('name', $json);
        $this->assertArrayHasKey('owner', $json);
        $this->assertArrayHasKey('add_user_list', $json);
        $this->assertArrayHasKey('del_user_list', $json);

        $this->assertIsString($json['chatid']);
        $this->assertIsString($json['name']);
        $this->assertIsString($json['owner']);
        $this->assertIsArray($json['add_user_list']);
        $this->assertIsArray($json['del_user_list']);
    }

    public function testUserListTypeConsistency(): void
    {
        $request = new UpdateAppChatRequest();
        $addUsers = ['add1', 'add2', 'add3'];
        $delUsers = ['del1', 'del2'];

        $request->setAddUserList($addUsers);
        $request->setDelUserList($delUsers);

        $retrievedAddUsers = $request->getAddUserList();
        $retrievedDelUsers = $request->getDelUserList();

        $this->assertSame($addUsers, $retrievedAddUsers);
        $this->assertSame($delUsers, $retrievedDelUsers);
        $this->assertIsArray($retrievedAddUsers);
        $this->assertIsArray($retrievedDelUsers);

        foreach ($retrievedAddUsers as $user) {
            $this->assertIsString($user);
        }

        foreach ($retrievedDelUsers as $user) {
            $this->assertIsString($user);
        }
    }

    public function testOnlyAddUsers(): void
    {
        $request = new UpdateAppChatRequest();
        $request->setChatId('only_add_chat');
        $request->setName('Only Add Users');
        $request->setOwner('owner');
        $request->setAddUserList(['new1', 'new2']);
        // Don't set del users (should default to empty)

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertSame(['new1', 'new2'], $options['json']['add_user_list']);
        $this->assertSame([], $options['json']['del_user_list']);
    }

    public function testOnlyDelUsers(): void
    {
        $request = new UpdateAppChatRequest();
        $request->setChatId('only_del_chat');
        $request->setName('Only Del Users');
        $request->setOwner('owner');
        $request->setDelUserList(['remove1', 'remove2']);
        // Don't set add users (should default to empty)

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertSame([], $options['json']['add_user_list']);
        $this->assertSame(['remove1', 'remove2'], $options['json']['del_user_list']);
    }
}
