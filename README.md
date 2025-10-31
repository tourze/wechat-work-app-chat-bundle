# Wechat Work App Chat Bundle

[![PHP Version](https://img.shields.io/badge/php-%5E8.1-blue)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/ci.yml?branch=master)](https://github.com/tourze/php-monorepo/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo)](https://codecov.io/gh/tourze/php-monorepo)

[English](README.md) | [中文](README.zh-CN.md)

Enterprise WeChat group chat management bundle for Symfony applications.

## Features

- Create and manage Enterprise WeChat group chats
- Send text, markdown, image, and file messages to group chats
- Automatic message queue with retry mechanism
- Symfony command-line tools for batch operations
- Event-driven message sending

## Requirements

- PHP 8.1+
- Symfony 6.4+
- Enterprise WeChat Work API credentials

## Installation

```bash
composer require tourze/wechat-work-app-chat-bundle
```

## Configuration

Register the bundle in your application:

```php
// config/bundles.php
return [
    // ...
    WechatWorkAppChatBundle\WechatWorkAppChatBundle::class => ['all' => true],
];
```

## Usage

### Services

#### AppChatService

Manages Enterprise WeChat group chats:

```php
use WechatWorkAppChatBundle\Service\AppChatService;

// Create a new group chat
$appChatService->createAppChat($chatId, $name, $owner, $userList);

// Update group chat information
$appChatService->updateAppChat($appChat);

// Sync group chat information from WeChat Work API
$appChatService->syncAppChat($appChat);

// Sync all unsynced group chats
$appChatService->syncUnsynced();
```

#### MessageService

Handles message sending to group chats:

```php
use WechatWorkAppChatBundle\Service\MessageService;

// Send text message
$messageService->sendText($chatId, $content, $mentionedList = [], $mentionedMobileList = []);

// Send markdown message
$messageService->sendMarkdown($chatId, $content);

// Send image message
$messageService->sendImage($chatId, $mediaId);

// Send file message
$messageService->sendFile($chatId, $mediaId);

// Send all unsent messages
$messageService->sendUnsent();
```

### Entities

- **AppChat**: Represents an Enterprise WeChat group chat
- **TextMessage**: Plain text messages with @mention support
- **MarkdownMessage**: Markdown formatted messages
- **ImageMessage**: Image messages with media ID
- **FileMessage**: File messages with media ID

### Commands

#### Send Unsent Messages

Sends all queued messages that haven't been sent yet:

```bash
bin/console wechat-work:app-chat:send-unsent
```

This command:
- Retrieves all unsent messages from the database
- Attempts to send each message via WeChat Work API
- Updates message status upon successful sending
- Handles errors gracefully with detailed output

#### Sync Group Chats

Synchronizes group chat information with WeChat Work API:

```bash
bin/console wechat-work:app-chat:sync
```

This command:
- Fetches all unsynced group chats
- Retrieves latest information from WeChat Work API
- Updates local database with current group chat details
- Reports sync status and any errors

### Event Subscribers

The bundle includes an automatic message sending subscriber that triggers when new messages are persisted to the database. This ensures messages are sent immediately when created.

## Examples

### Creating and Sending Messages

```php
// Create a text message
$textMessage = new TextMessage();
$textMessage->setChatId('your-chat-id');
$textMessage->setContent('Hello, World!');
$textMessage->setMentionedList(['@all']); // Mention everyone

// Save to database (automatically triggers sending)
$entityManager->persist($textMessage);
$entityManager->flush();

// Send markdown message directly
$messageService->sendMarkdown('your-chat-id', '# Important Notice\n\nPlease check the latest updates.');
```

### Managing Group Chats

```php
// Create a new group chat
$appChat = new AppChat();
$appChat->setChatId('tech-team-001');
$appChat->setName('Tech Team Discussion');
$appChat->setOwner('john.doe');
$appChat->setUserList(['john.doe', 'jane.smith', 'bob.wilson']);

$entityManager->persist($appChat);
$entityManager->flush();

// Sync with WeChat Work API
$appChatService->syncAppChat($appChat);
```

## Testing

Run the test suite:

```bash
./vendor/bin/phpunit packages/wechat-work-app-chat-bundle/tests
```

## Contributing

Please read the main repository's contributing guidelines before submitting pull requests.

## License

This bundle is licensed under the same license as the parent monorepo.