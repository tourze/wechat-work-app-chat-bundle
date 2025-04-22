<?php

namespace WechatWorkAppChatBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkAppChatBundle\Entity\BaseChatMessage;
use WechatWorkBundle\Request\AgentAware;

class SendAppChatMessageRequest extends ApiRequest
{
    use AgentAware;

    private BaseChatMessage $message;

    public function getRequestPath(): string
    {
        return '/cgi-bin/appchat/send';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'json' => array_merge([
                'chatid' => $this->getMessage()->getAppChat()->getChatId(),
                'msgtype' => $this->getMessage()->getMsgType(),
            ], $this->getMessage()->getRequestContent()),
        ];
    }

    public function getMessage(): BaseChatMessage
    {
        return $this->message;
    }

    public function setMessage(BaseChatMessage $message): void
    {
        $this->message = $message;
        $this->setAgent($message->getAppChat()->getAgent());
    }
}
