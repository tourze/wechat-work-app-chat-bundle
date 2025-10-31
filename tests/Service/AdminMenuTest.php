<?php

declare(strict_types=1);

namespace WechatWorkAppChatBundle\Tests\Service;

use Knp\Menu\ItemInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;
use WechatWorkAppChatBundle\Entity\AppChat;
use WechatWorkAppChatBundle\Entity\FileMessage;
use WechatWorkAppChatBundle\Entity\ImageMessage;
use WechatWorkAppChatBundle\Entity\MarkdownMessage;
use WechatWorkAppChatBundle\Entity\TextMessage;
use WechatWorkAppChatBundle\Service\AdminMenu;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    private AdminMenu $adminMenu;

    private LinkGeneratorInterface&MockObject $linkGenerator;

    private ItemInterface&MockObject $rootItem;

    protected function onSetUp(): void
    {
        $this->linkGenerator = $this->createMock(LinkGeneratorInterface::class);

        // 在集成测试中，我们应该从容器获取服务而不是直接实例化
        // 但我们需要先注入mock到容器，然后获取服务
        self::getContainer()->set(LinkGeneratorInterface::class, $this->linkGenerator);
        $this->adminMenu = self::getService(AdminMenu::class);
        $this->rootItem = $this->createMock(ItemInterface::class);
    }

    public function testInvokeCreatesWechatManagementMenu(): void
    {
        $wechatMenu = $this->createMock(ItemInterface::class);

        // 模拟根菜单没有企业微信管理子菜单，然后有了
        $this->rootItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('企业微信管理')
            ->willReturnOnConsecutiveCalls(null, $wechatMenu)
        ;

        $this->rootItem->expects($this->once())
            ->method('addChild')
            ->with('企业微信管理')
            ->willReturn($wechatMenu)
        ;

        $wechatMenu->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fa fa-wechat')
            ->willReturnSelf()
        ;

        $appChatMenu = $this->createMock(ItemInterface::class);

        // 模拟群聊管理子菜单，先不存在，然后存在
        $wechatMenu->expects($this->exactly(2))
            ->method('getChild')
            ->with('群聊管理')
            ->willReturnOnConsecutiveCalls(null, $appChatMenu)
        ;

        $wechatMenu->expects($this->once())
            ->method('addChild')
            ->with('群聊管理')
            ->willReturn($appChatMenu)
        ;

        $appChatMenu->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fa fa-comments')
            ->willReturnSelf()
        ;

        // 配置链接生成器的预期调用
        $this->linkGenerator->expects($this->exactly(5))
            ->method('getCurdListPage')
            ->willReturnCallback(function (string $entityClass): string {
                return match ($entityClass) {
                    AppChat::class => 'http://localhost/admin?crudAction=index&crudControllerFqcn=WechatWorkAppChatBundle%5CEntity%5CAppChat',
                    TextMessage::class => 'http://localhost/admin?crudAction=index&crudControllerFqcn=WechatWorkAppChatBundle%5CEntity%5CTextMessage',
                    ImageMessage::class => 'http://localhost/admin?crudAction=index&crudControllerFqcn=WechatWorkAppChatBundle%5CEntity%5CImageMessage',
                    FileMessage::class => 'http://localhost/admin?crudAction=index&crudControllerFqcn=WechatWorkAppChatBundle%5CEntity%5CFileMessage',
                    MarkdownMessage::class => 'http://localhost/admin?crudAction=index&crudControllerFqcn=WechatWorkAppChatBundle%5CEntity%5CMarkdownMessage',
                    default => '/admin/unknown',
                };
            })
        ;

        // 配置子菜单项的创建
        $this->setupMenuItemExpectations($appChatMenu);

        // 调用测试方法
        ($this->adminMenu)($this->rootItem);
    }

    public function testInvokeWithExistingWechatMenu(): void
    {
        $wechatMenu = $this->createMock(ItemInterface::class);

        // 模拟企业微信管理菜单已存在
        $this->rootItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('企业微信管理')
            ->willReturn($wechatMenu)
        ;

        $appChatMenu = $this->createMock(ItemInterface::class);

        // 模拟群聊管理子菜单不存在，然后存在
        $wechatMenu->expects($this->exactly(2))
            ->method('getChild')
            ->with('群聊管理')
            ->willReturnOnConsecutiveCalls(null, $appChatMenu)
        ;

        $wechatMenu->expects($this->once())
            ->method('addChild')
            ->with('群聊管理')
            ->willReturn($appChatMenu)
        ;

        $appChatMenu->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fa fa-comments')
            ->willReturnSelf()
        ;

        // 配置链接生成器
        $this->linkGenerator->expects($this->exactly(5))
            ->method('getCurdListPage')
            ->willReturnCallback(function (string $entityClass): string {
                return match ($entityClass) {
                    AppChat::class => 'http://localhost/admin?crudAction=index&crudControllerFqcn=WechatWorkAppChatBundle%5CEntity%5CAppChat',
                    TextMessage::class => 'http://localhost/admin?crudAction=index&crudControllerFqcn=WechatWorkAppChatBundle%5CEntity%5CTextMessage',
                    ImageMessage::class => 'http://localhost/admin?crudAction=index&crudControllerFqcn=WechatWorkAppChatBundle%5CEntity%5CImageMessage',
                    FileMessage::class => 'http://localhost/admin?crudAction=index&crudControllerFqcn=WechatWorkAppChatBundle%5CEntity%5CFileMessage',
                    MarkdownMessage::class => 'http://localhost/admin?crudAction=index&crudControllerFqcn=WechatWorkAppChatBundle%5CEntity%5CMarkdownMessage',
                    default => '/admin/unknown',
                };
            })
        ;

        // 配置子菜单项的创建
        $this->setupMenuItemExpectations($appChatMenu);

        // 调用测试方法
        ($this->adminMenu)($this->rootItem);
    }

    public function testInvokeExitsEarlyWhenWechatMenuNotFound(): void
    {
        $wechatMenu = $this->createMock(ItemInterface::class);

        // 第一次调用getChild返回null（菜单不存在），第二次返回null（仍然不存在）
        $this->rootItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('企业微信管理')
            ->willReturnOnConsecutiveCalls(null, null)
        ;

        $this->rootItem->expects($this->once())
            ->method('addChild')
            ->with('企业微信管理')
            ->willReturn($wechatMenu)
        ;

        $wechatMenu->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fa fa-wechat')
            ->willReturnSelf()
        ;

        // 不应该调用linkGenerator，因为早期退出
        $this->linkGenerator->expects($this->never())
            ->method('getCurdListPage')
        ;

        // 调用测试方法
        ($this->adminMenu)($this->rootItem);
    }

    public function testInvokeExitsEarlyWhenAppChatMenuNotFound(): void
    {
        $wechatMenu = $this->createMock(ItemInterface::class);

        // 企业微信管理菜单存在
        $this->rootItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('企业微信管理')
            ->willReturn($wechatMenu)
        ;

        $appChatMenu = $this->createMock(ItemInterface::class);

        // 群聊管理菜单不存在，添加后getChild仍返回null
        $wechatMenu->expects($this->exactly(2))
            ->method('getChild')
            ->with('群聊管理')
            ->willReturnOnConsecutiveCalls(null, null)
        ;

        $wechatMenu->expects($this->once())
            ->method('addChild')
            ->with('群聊管理')
            ->willReturn($appChatMenu)
        ;

        $appChatMenu->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fa fa-comments')
            ->willReturnSelf()
        ;

        // 不应该调用linkGenerator，因为早期退出
        $this->linkGenerator->expects($this->never())
            ->method('getCurdListPage')
        ;

        // 调用测试方法
        ($this->adminMenu)($this->rootItem);
    }

    private function setupMenuItemExpectations(ItemInterface&MockObject $appChatMenu): void
    {
        $menuItems = [
            ['群聊会话', 'http://localhost/admin?crudAction=index&crudControllerFqcn=WechatWorkAppChatBundle%5CEntity%5CAppChat', 'fa fa-comments-o'],
            ['文本消息', 'http://localhost/admin?crudAction=index&crudControllerFqcn=WechatWorkAppChatBundle%5CEntity%5CTextMessage', 'fa fa-font'],
            ['图片消息', 'http://localhost/admin?crudAction=index&crudControllerFqcn=WechatWorkAppChatBundle%5CEntity%5CImageMessage', 'fa fa-image'],
            ['文件消息', 'http://localhost/admin?crudAction=index&crudControllerFqcn=WechatWorkAppChatBundle%5CEntity%5CFileMessage', 'fa fa-file'],
            ['Markdown消息', 'http://localhost/admin?crudAction=index&crudControllerFqcn=WechatWorkAppChatBundle%5CEntity%5CMarkdownMessage', 'fa fa-code'],
        ];

        $childMenus = [];
        foreach ($menuItems as [$title, $uri, $icon]) {
            $childMenu = $this->createMock(ItemInterface::class);
            $childMenus[] = $childMenu;

            $childMenu->expects($this->once())
                ->method('setUri')
                ->with($uri)
                ->willReturnSelf()
            ;

            $childMenu->expects($this->once())
                ->method('setAttribute')
                ->with('icon', $icon)
                ->willReturnSelf()
            ;
        }

        $appChatMenu->expects($this->exactly(5))
            ->method('addChild')
            ->willReturnCallback(function (string $title) use ($menuItems, $childMenus): ItemInterface {
                foreach ($menuItems as $index => [$expectedTitle]) {
                    if ($title === $expectedTitle) {
                        return $childMenus[$index];
                    }
                }
                throw new \InvalidArgumentException("Unexpected menu title: {$title}");
            })
        ;
    }
}
