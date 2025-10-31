<?php

declare(strict_types=1);

namespace WechatWorkAppChatBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use WechatWorkAppChatBundle\Entity\TextMessage;

/**
 * @extends AbstractCrudController<TextMessage>
 */
#[AdminCrud(routePath: '/wechat-work-app-chat/text-messages', routeName: 'wechat_work_app_chat_text_messages')]
final class TextMessageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TextMessage::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('文本消息')
            ->setEntityLabelInPlural('文本消息')
            ->setPageTitle('index', '企业微信群聊文本消息管理')
            ->setPageTitle('new', '新建文本消息')
            ->setPageTitle('edit', '编辑文本消息')
            ->setPageTitle('detail', '文本消息详情')
            ->setDefaultSort(['id' => 'DESC'])
            ->setPaginatorPageSize(20)
            ->setSearchFields(['content', 'msgId'])
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
            ->add('appChat')
            ->add('isSent')
            ->add('isRecalled')
            ->add('createTime')
            ->add('sentAt')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnDetail()
        ;

        yield AssociationField::new('appChat', '群聊会话')
            ->setRequired(true)
            ->setHelp('选择关联的群聊会话')
        ;

        yield TextareaField::new('content', '消息内容')
            ->setRequired(true)
            ->setMaxLength(4096)
            ->setHelp('文本消息内容，最多4096个字符')
            ->setNumOfRows(6)
        ;

        yield BooleanField::new('isSent', '发送状态')
            ->renderAsSwitch(false)
            ->setHelp('消息是否已发送')
        ;

        yield TextField::new('msgId', '消息ID')
            ->setMaxLength(64)
            ->setHelp('企业微信返回的消息ID')
            ->hideOnIndex()
        ;

        yield DateTimeField::new('sentAt', '发送时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->setHelp('消息发送时间')
            ->hideOnIndex()
        ;

        yield BooleanField::new('isRecalled', '撤回状态')
            ->renderAsSwitch(false)
            ->setHelp('消息是否已撤回')
            ->hideOnIndex()
        ;

        yield DateTimeField::new('recalledAt', '撤回时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->setHelp('消息撤回时间')
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
