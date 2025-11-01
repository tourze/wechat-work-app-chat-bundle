<?php

namespace WechatWorkAppChatBundle\Tests\Request;

use HttpClientBundle\Test\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkAppChatBundle\Request\CreateAppChatRequest;

/**
 * @internal
 */
#[CoversClass(CreateAppChatRequest::class)]
final class CreateAppChatRequestTest extends RequestTestCase
{
    public function testCreateRequest(): void
    {
        $agent = $this->createMock(AgentInterface::class);

        $request = new CreateAppChatRequest();
        $request->setAgent($agent);
        $request->setName('测试群聊');
        $request->setOwner('owner_user_id');
        $request->setUserList(['user1', 'user2', 'user3']);

        $this->assertSame($agent, $request->getAgent());
        $this->assertSame('测试群聊', $request->getName());
        $this->assertSame('owner_user_id', $request->getOwner());
        $this->assertSame(['user1', 'user2', 'user3'], $request->getUserList());
    }

    public function testGetRequestPath(): void
    {
        $request = new CreateAppChatRequest();
        $this->assertSame('/cgi-bin/appchat/create', $request->getRequestPath());
    }

    public function testGetRequestOptions(): void
    {
        $request = new CreateAppChatRequest();
        $request->setName('测试群聊');
        $request->setOwner('test_owner');
        $request->setUserList(['user1', 'user2']);

        $expected = [
            'json' => [
                'name' => '测试群聊',
                'owner' => 'test_owner',
                'userlist' => ['user1', 'user2'],
            ],
        ];

        $this->assertSame($expected, $request->getRequestOptions());
    }

    public function testEmptyUserList(): void
    {
        $request = new CreateAppChatRequest();
        $request->setName('Empty User List');
        $request->setOwner('owner');
        $request->setUserList([]);

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertArrayHasKey('userlist', $options['json']);
        $this->assertSame([], $options['json']['userlist']);
    }

    public function testSingleUserInList(): void
    {
        $request = new CreateAppChatRequest();
        $request->setName('Single User');
        $request->setOwner('owner');
        $request->setUserList(['single_user']);

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertSame(['single_user'], $options['json']['userlist']);
    }

    public function testMultipleUsersInList(): void
    {
        $request = new CreateAppChatRequest();
        $request->setName('Multiple Users');
        $request->setOwner('owner');
        $userList = ['user1', 'user2', 'user3', 'user4', 'user5'];
        $request->setUserList($userList);

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertSame($userList, $options['json']['userlist']);
        $this->assertCount(5, $options['json']['userlist']);
    }

    public function testChineseCharactersInName(): void
    {
        $request = new CreateAppChatRequest();
        $request->setName('中文群聊名称测试');
        $request->setOwner('owner');
        $request->setUserList(['user1']);

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertSame('中文群聊名称测试', $options['json']['name']);
    }

    public function testSpecialCharactersInOwner(): void
    {
        $request = new CreateAppChatRequest();
        $request->setName('Test Chat');
        $request->setOwner('owner_with-special.chars_123');
        $request->setUserList(['user1']);

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertSame('owner_with-special.chars_123', $options['json']['owner']);
    }

    public function testLongUserList(): void
    {
        $request = new CreateAppChatRequest();
        $request->setName('Long User List');
        $request->setOwner('owner');

        // Create a list with many users
        $userList = [];
        for ($i = 1; $i <= 50; ++$i) {
            $userList[] = "user{$i}";
        }
        $request->setUserList($userList);

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $json = $options['json'];
        $this->assertArrayHasKey('userlist', $json);
        $this->assertIsArray($json['userlist']);
        $this->assertCount(50, $json['userlist']);
        $this->assertSame('user1', $json['userlist'][0]);
        $this->assertSame('user50', $json['userlist'][49]);
    }

    public function testRequestStructure(): void
    {
        $request = new CreateAppChatRequest();
        $request->setName('Structure Test');
        $request->setOwner('owner');
        $request->setUserList(['user1', 'user2']);

        $options = $request->getRequestOptions();

        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);

        /** @var array<string, mixed> $json */
        $json = $options['json'];
        $this->assertArrayHasKey('name', $json);
        $this->assertArrayHasKey('owner', $json);
        $this->assertArrayHasKey('userlist', $json);

        $this->assertIsString($json['name']);
        $this->assertIsString($json['owner']);
        $this->assertIsArray($json['userlist']);
    }

    public function testUserListTypeConsistency(): void
    {
        $request = new CreateAppChatRequest();
        $userList = ['user1', 'user2', 'user3'];
        $request->setUserList($userList);

        $retrievedList = $request->getUserList();
        $this->assertSame($userList, $retrievedList);
        $this->assertIsArray($retrievedList);

        foreach ($retrievedList as $user) {
            $this->assertIsString($user);
        }
    }
}
