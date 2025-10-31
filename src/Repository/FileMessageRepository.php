<?php

namespace WechatWorkAppChatBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use WechatWorkAppChatBundle\Entity\FileMessage;

/**
 * @extends ServiceEntityRepository<FileMessage>
 */
#[AsRepository(entityClass: FileMessage::class)]
class FileMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FileMessage::class);
    }

    /**
     * @return FileMessage[]
     */
    public function findUnsent(): array
    {
        /** @var array<FileMessage> */
        return $this->createQueryBuilder('fm')
            ->andWhere('fm.isSent = :isSent')
            ->setParameter('isSent', false)
            ->orderBy('fm.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function save(FileMessage $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FileMessage $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
