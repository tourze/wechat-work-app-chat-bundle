<?php

namespace WechatWorkAppChatBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use WechatWorkAppChatBundle\Entity\TextMessage;

/**
 * @extends ServiceEntityRepository<TextMessage>
 */
#[AsRepository(entityClass: TextMessage::class)]
class TextMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TextMessage::class);
    }

    /**
     * @return TextMessage[]
     */
    public function findUnsent(): array
    {
        /** @var array<TextMessage> */
        return $this->createQueryBuilder('tm')
            ->andWhere('tm.isSent = :isSent')
            ->setParameter('isSent', false)
            ->orderBy('tm.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function save(TextMessage $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TextMessage $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
