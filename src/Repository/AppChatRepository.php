<?php

namespace WechatWorkAppChatBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use WechatWorkAppChatBundle\Entity\AppChat;

/**
 * @extends ServiceEntityRepository<AppChat>
 */
#[AsRepository(entityClass: AppChat::class)]
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

    /**
     * @return AppChat[]
     */
    public function findUnsynced(): array
    {
        /** @var array<AppChat> */
        return $this->createQueryBuilder('ac')
            ->andWhere('ac.isSynced = :isSynced')
            ->setParameter('isSynced', false)
            ->getQuery()
            ->getResult()
        ;
    }

    public function save(AppChat $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AppChat $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
