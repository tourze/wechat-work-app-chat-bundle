<?php

namespace WechatWorkAppChatBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WechatWorkAppChatBundle\Service\AppChatService;

#[AsCommand(
    name: 'wechat-work:app-chat:sync',
    description: '同步企业微信群聊信息',
)]
class SyncAppChatCommand extends Command
{
    public function __construct(
        private readonly AppChatService $appChatService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('开始同步企业微信群聊信息...');

        try {
            $this->appChatService->syncUnsynced();
            $output->writeln('同步完成');

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln(sprintf('同步失败：%s', $e->getMessage()));

            return Command::FAILURE;
        }
    }
}
