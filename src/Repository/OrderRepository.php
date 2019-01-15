<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry,EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Order::class);
        $this->entityManager = $entityManager;
    }


    public function findDraftOrderByUser($user): ?Order
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.userId = :userId')
            ->setParameter('userId', $user)
            ->andWhere('o.status = :status')
            ->setParameter('status', 'draft')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }


    public function getAllOrdersCount()
    {
        return $this->createQueryBuilder('o')
            ->select('count(o.id)')
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    public function getDraftOrdersCount()
    {
        return $this->createQueryBuilder('o')
            ->select('count(o.id)')
            ->andWhere('o.status = :status')
            ->setParameter('status', 'draft')
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    public function getPendingOrdersCount()
    {
        return $this->createQueryBuilder('o')
            ->select('count(o.id)')
            ->andWhere('o.status = :status')
            ->setParameter('status', 'pending')
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    public function getConfirmedOrdersCount()
    {
        return $this->createQueryBuilder('o')
            ->select('count(o.id)')
            ->andWhere('o.status = :status')
            ->setParameter('status', 'confirmed')
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }


    public function save(Order $order): void
    {
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }

}
