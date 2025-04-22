<?php

namespace WechatWorkAppChatBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatWorkAppChatBundle\Entity\FileMessage;

/**
 * @method FileMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method FileMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method FileMessage[]    findAll()
 * @method FileMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FileMessage::class);
    }

    public function findUnsent(): array
    {
        return $this->createQueryBuilder('fm')
            ->andWhere('fm.isSent = :isSent')
            ->setParameter('isSent', false)
            ->orderBy('fm.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
