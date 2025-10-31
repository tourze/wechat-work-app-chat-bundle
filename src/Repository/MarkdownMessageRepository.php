<?php

namespace WechatWorkAppChatBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use WechatWorkAppChatBundle\Entity\MarkdownMessage;

/**
 * @extends ServiceEntityRepository<MarkdownMessage>
 */
#[AsRepository(entityClass: MarkdownMessage::class)]
class MarkdownMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarkdownMessage::class);
    }

    /**
     * @return MarkdownMessage[]
     */
    public function findUnsent(): array
    {
        /** @var array<MarkdownMessage> */
        return $this->createQueryBuilder('mm')
            ->andWhere('mm.isSent = :isSent')
            ->setParameter('isSent', false)
            ->orderBy('mm.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function save(MarkdownMessage $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MarkdownMessage $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
