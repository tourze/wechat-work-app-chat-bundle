<?php

namespace WechatWorkAppChatBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatWorkAppChatBundle\Entity\AppChat;

/**
 * @method AppChat|null find($id, $lockMode = null, $lockVersion = null)
 * @method AppChat|null findOneBy(array $criteria, array $orderBy = null)
 * @method AppChat[]    findAll()
 * @method AppChat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppChatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppChat::class);
    }

    public function findByChatId(string $chatId): ?AppChat
    {
        return $this->findOneBy(['chatId' => $chatId]);
    }

    public function findUnsynced(): array
    {
        return $this->createQueryBuilder('ac')
            ->andWhere('ac.isSynced = :isSynced')
            ->setParameter('isSynced', false)
            ->getQuery()
            ->getResult();
    }
}
