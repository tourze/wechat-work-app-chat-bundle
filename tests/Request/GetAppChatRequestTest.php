<?php

namespace WechatWorkAppChatBundle\Tests\Request;

use HttpClientBundle\Tests\Request\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkAppChatBundle\Request\GetAppChatRequest;

/**
 * @internal
 */
#[CoversClass(GetAppChatRequest::class)]
final class GetAppChatRequestTest extends RequestTestCase
{
    public function testCreateRequest(): void
    {
        $agent = $this->createMock(AgentInterface::class);

        $request = new GetAppChatRequest();
        $request->setAgent($agent);
        $request->setChatId('test_chat_123');

        $this->assertSame($agent, $request->getAgent());
        $this->assertSame('test_chat_123', $request->getChatId());
    }

    public function testGetRequestPath(): void
    {
        $request = new GetAppChatRequest();
        $this->assertSame('/cgi-bin/appchat/get', $request->getRequestPath());
    }

    public function testGetRequestOptions(): void
    {
        $request = new GetAppChatRequest();
        $request->setChatId('sample_chat_id');

        $expected = [
            'query' => [
                'chatid' => 'sample_chat_id',
            ],
        ];

        $this->assertSame($expected, $request->getRequestOptions());
    }

    public function testChatIdWithSpecialCharacters(): void
    {
        $request = new GetAppChatRequest();
        $chatId = 'chat_with-special.chars_123';
        $request->setChatId($chatId);

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertIsArray($options['query']);
        $this->assertSame($chatId, $options['query']['chatid']);
    }

    public function testChatIdWithNumbers(): void
    {
        $request = new GetAppChatRequest();
        $chatId = '1234567890';
        $request->setChatId($chatId);

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertIsArray($options['query']);
        $this->assertSame($chatId, $options['query']['chatid']);
    }

    public function testLongChatId(): void
    {
        $request = new GetAppChatRequest();
        $chatId = str_repeat('a', 100); // Very long chat ID
        $request->setChatId($chatId);

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertIsArray($options['query']);
        $this->assertSame($chatId, $options['query']['chatid']);
    }

    public function testShortChatId(): void
    {
        $request = new GetAppChatRequest();
        $chatId = 'a';
        $request->setChatId($chatId);

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertIsArray($options['query']);
        $this->assertSame($chatId, $options['query']['chatid']);
    }

    public function testRequestStructure(): void
    {
        $request = new GetAppChatRequest();
        $request->setChatId('structure_test');

        $options = $request->getRequestOptions();

        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertArrayHasKey('query', $options);

        /** @var array<string, mixed> $query */
        $query = $options['query'];
        $this->assertArrayHasKey('chatid', $query);
        $this->assertIsString($query['chatid']);
    }

    public function testChatIdConsistency(): void
    {
        $request = new GetAppChatRequest();
        $originalChatId = 'consistency_test_chat';
        $request->setChatId($originalChatId);

        $retrievedChatId = $request->getChatId();
        $this->assertSame($originalChatId, $retrievedChatId);
        $this->assertIsString($retrievedChatId);
    }

    public function testChatIdWithUnderscores(): void
    {
        $request = new GetAppChatRequest();
        $chatId = 'chat_with_multiple_underscores_here';
        $request->setChatId($chatId);

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertIsArray($options['query']);
        $this->assertSame($chatId, $options['query']['chatid']);
    }

    public function testChatIdWithHyphens(): void
    {
        $request = new GetAppChatRequest();
        $chatId = 'chat-with-multiple-hyphens-here';
        $request->setChatId($chatId);

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertIsArray($options['query']);
        $this->assertSame($chatId, $options['query']['chatid']);
    }

    public function testAlphanumericChatId(): void
    {
        $request = new GetAppChatRequest();
        $chatId = 'chat123ABC456def';
        $request->setChatId($chatId);

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertIsArray($options['query']);
        $this->assertSame($chatId, $options['query']['chatid']);
    }

    public function testQueryParamStructure(): void
    {
        $request = new GetAppChatRequest();
        $request->setChatId('query_test');

        $options = $request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertIsArray($options['query']);
        $query = $options['query'];

        // Ensure only the chatid parameter is present
        $this->assertCount(1, $query);
        $this->assertArrayHasKey('chatid', $query);
        $this->assertArrayNotHasKey('name', $query);
        $this->assertArrayNotHasKey('owner', $query);
    }
}
