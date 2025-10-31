<?php

declare(strict_types=1);

namespace WechatWorkAppChatBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use WechatWorkAppChatBundle\WechatWorkAppChatBundle;

/**
 * @internal
 */
#[CoversClass(WechatWorkAppChatBundle::class)]
#[RunTestsInSeparateProcesses]
final class WechatWorkAppChatBundleTest extends AbstractBundleTestCase
{
}
