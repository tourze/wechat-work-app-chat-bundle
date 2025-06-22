<?php

namespace WechatWorkAppChatBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WechatWorkAppChatBundle\Service\MessageService;

#[AsCommand(
    name: self::NAME,
    description: '发送未发送的企业微信群聊消息',
)]
class SendUnsentMessagesCommand extends Command
{
    public const NAME = 'wechat-work:app-chat:send-unsent';

    public function __construct(
        private readonly MessageService $messageService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('开始发送未发送的企业微信群聊消息...');

        try {
            $this->messageService->sendUnsent();
            $output->writeln('发送完成');

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln(sprintf('发送失败：%s', $e->getMessage()));

            return Command::FAILURE;
        }
    }
}
