<?php

namespace WechatWorkAppChatBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use WechatWorkAppChatBundle\Entity\ImageMessage;

/**
 * @extends ServiceEntityRepository<ImageMessage>
 */
#[AsRepository(entityClass: ImageMessage::class)]
class ImageMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImageMessage::class);
    }

    /**
     * @return ImageMessage[]
     */
    public function findUnsent(): array
    {
        /** @var array<ImageMessage> */
        return $this->createQueryBuilder('im')
            ->andWhere('im.isSent = :isSent')
            ->setParameter('isSent', false)
            ->orderBy('im.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function save(ImageMessage $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ImageMessage $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
