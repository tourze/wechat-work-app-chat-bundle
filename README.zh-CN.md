# 企业微信群聊管理包

[![PHP Version](https://img.shields.io/badge/php-%5E8.1-blue)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/ci.yml?branch=master)](https://github.com/tourze/php-monorepo/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo)](https://codecov.io/gh/tourze/php-monorepo)

[English](README.md) | [中文](README.zh-CN.md)

用于 Symfony 应用程序的企业微信群聊管理包。

## 功能特性

- 创建和管理企业微信群聊
- 发送文本、Markdown、图片和文件消息到群聊
- 自动消息队列与重试机制
- Symfony 命令行工具批量操作
- 事件驱动的消息发送

## 系统要求

- PHP 8.1+
- Symfony 6.4+
- 企业微信 API 凭证

## 安装

```bash
composer require tourze/wechat-work-app-chat-bundle
```

## 配置

在应用程序中注册包：

```php
// config/bundles.php
return [
    // ...
    WechatWorkAppChatBundle\WechatWorkAppChatBundle::class => ['all' => true],
];
```

## 使用方法

### 服务

#### AppChatService

管理企业微信群聊：

```php
use WechatWorkAppChatBundle\Service\AppChatService;

// 创建新群聊
$appChatService->createAppChat($chatId, $name, $owner, $userList);

// 更新群聊信息
$appChatService->updateAppChat($appChat);

// 从企业微信 API 同步群聊信息
$appChatService->syncAppChat($appChat);

// 同步所有未同步的群聊
$appChatService->syncUnsynced();
```

#### MessageService

处理向群聊发送消息：

```php
use WechatWorkAppChatBundle\Service\MessageService;

// 发送文本消息
$messageService->sendText($chatId, $content, $mentionedList = [], $mentionedMobileList = []);

// 发送 Markdown 消息
$messageService->sendMarkdown($chatId, $content);

// 发送图片消息
$messageService->sendImage($chatId, $mediaId);

// 发送文件消息
$messageService->sendFile($chatId, $mediaId);

// 发送所有未发送的消息
$messageService->sendUnsent();
```

### 实体

- **AppChat**: 表示企业微信群聊
- **TextMessage**: 支持@提醒的纯文本消息
- **MarkdownMessage**: Markdown 格式消息
- **ImageMessage**: 带媒体 ID 的图片消息
- **FileMessage**: 带媒体 ID 的文件消息

### 命令

#### 发送未发送的消息

发送所有尚未发送的队列消息：

```bash
bin/console wechat-work:app-chat:send-unsent
```

此命令功能：
- 从数据库检索所有未发送的消息
- 尝试通过企业微信 API 发送每条消息
- 成功发送后更新消息状态
- 优雅地处理错误并提供详细输出

#### 同步群聊

将群聊信息与企业微信 API 同步：

```bash
bin/console wechat-work:app-chat:sync
```

此命令功能：
- 获取所有未同步的群聊
- 从企业微信 API 检索最新信息
- 使用当前群聊详情更新本地数据库
- 报告同步状态和任何错误

### 事件订阅器

该包包含一个自动消息发送订阅器，当新消息持久化到数据库时触发。这确保了消息在创建时立即发送。

## 示例

### 创建和发送消息

```php
// 创建文本消息
$textMessage = new TextMessage();
$textMessage->setChatId('your-chat-id');
$textMessage->setContent('你好，世界！');
$textMessage->setMentionedList(['@all']); // 提醒所有人

// 保存到数据库（自动触发发送）
$entityManager->persist($textMessage);
$entityManager->flush();

// 直接发送 Markdown 消息
$messageService->sendMarkdown('your-chat-id', '# 重要通知\n\n请查看最新更新。');
```

### 管理群聊

```php
// 创建新群聊
$appChat = new AppChat();
$appChat->setChatId('tech-team-001');
$appChat->setName('技术团队讨论组');
$appChat->setOwner('john.doe');
$appChat->setUserList(['john.doe', 'jane.smith', 'bob.wilson']);

$entityManager->persist($appChat);
$entityManager->flush();

// 与企业微信 API 同步
$appChatService->syncAppChat($appChat);
```

## 测试

运行测试套件：

```bash
./vendor/bin/phpunit packages/wechat-work-app-chat-bundle/tests
```

## 贡献

在提交拉取请求之前，请阅读主仓库的贡献指南。

## 许可证

此包使用与父 monorepo 相同的许可证。