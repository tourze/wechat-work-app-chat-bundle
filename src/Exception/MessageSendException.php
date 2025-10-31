<?php

namespace WechatWorkAppChatBundle\Exception;

class MessageSendException extends \Exception
{
    public function __construct(string $message, string $chatId = '', ?\Throwable $previous = null)
    {
        $fullMessage = '' !== $chatId ? "Chat {$chatId}: {$message}" : $message;
        parent::__construct($fullMessage, 0, $previous);
    }
}
