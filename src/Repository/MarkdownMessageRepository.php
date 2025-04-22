<?php

namespace WechatWorkAppChatBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatWorkAppChatBundle\Entity\MarkdownMessage;

/**
 * @method MarkdownMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method MarkdownMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method MarkdownMessage[]    findAll()
 * @method MarkdownMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarkdownMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarkdownMessage::class);
    }

    public function findUnsent(): array
    {
        return $this->createQueryBuilder('mm')
            ->andWhere('mm.isSent = :isSent')
            ->setParameter('isSent', false)
            ->orderBy('mm.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
