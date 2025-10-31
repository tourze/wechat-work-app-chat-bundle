<?php

declare(strict_types=1);

namespace WechatWorkAppChatBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use WechatWorkAppChatBundle\Entity\AppChat;

/**
 * @extends AbstractCrudController<AppChat>
 */
#[AdminCrud(routePath: '/wechat-work-app-chat/app-chats', routeName: 'wechat_work_app_chat_app_chats')]
final class AppChatCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AppChat::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('群聊会话')
            ->setEntityLabelInPlural('群聊会话')
            ->setPageTitle('index', '企业微信群聊会话管理')
            ->setPageTitle('new', '新建群聊会话')
            ->setPageTitle('edit', '编辑群聊会话')
            ->setPageTitle('detail', '群聊会话详情')
            ->setDefaultSort(['id' => 'DESC'])
            ->setPaginatorPageSize(20)
            ->setSearchFields(['chatId', 'name', 'owner'])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->setPermission(Action::NEW, 'ROLE_ADMIN')
            ->setPermission(Action::EDIT, 'ROLE_ADMIN')
            ->setPermission(Action::DELETE, 'ROLE_ADMIN')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('agent')
            ->add('isSynced')
            ->add('createTime')
            ->add('updateTime')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnDetail()
        ;

        yield AssociationField::new('agent', '企业微信应用')
            ->setRequired(true)
            ->setHelp('选择关联的企业微信应用')
        ;

        yield TextField::new('chatId', '群聊ID')
            ->setRequired(true)
            ->setMaxLength(32)
            ->setHelp('企业微信群聊唯一标识，最多32个字符')
        ;

        yield TextField::new('name', '群聊名称')
            ->setRequired(true)
            ->setMaxLength(32)
            ->setHelp('群聊显示名称，最多32个字符')
        ;

        yield TextField::new('owner', '群主UserID')
            ->setRequired(true)
            ->setMaxLength(32)
            ->setHelp('群主的企业微信UserID，最多32个字符')
        ;

        yield ArrayField::new('userList', '群成员列表')
            ->setRequired(true)
            ->setHelp('群成员的企业微信UserID列表')
            ->hideOnIndex()
        ;

        yield BooleanField::new('isSynced', '同步状态')
            ->renderAsSwitch(false)
            ->setHelp('是否已同步到企业微信')
        ;

        yield DateTimeField::new('lastSyncedAt', '最后同步时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->setHelp('最后一次同步到企业微信的时间')
            ->hideOnIndex()
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->onlyOnDetail()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->onlyOnDetail()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield TextField::new('createBy', '创建者')
            ->onlyOnDetail()
        ;

        yield TextField::new('updateBy', '更新者')
            ->onlyOnDetail()
        ;
    }
}
