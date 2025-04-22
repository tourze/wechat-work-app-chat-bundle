<?php

namespace WechatWorkAppChatBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatWorkAppChatBundle\Entity\TextMessage;

/**
 * @method TextMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method TextMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method TextMessage[]    findAll()
 * @method TextMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TextMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TextMessage::class);
    }

    public function findUnsent(): array
    {
        return $this->createQueryBuilder('tm')
            ->andWhere('tm.isSent = :isSent')
            ->setParameter('isSent', false)
            ->orderBy('tm.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
