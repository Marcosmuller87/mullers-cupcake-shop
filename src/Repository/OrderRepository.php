<?php
namespace App\Repository;
use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findOneWithUser(int $id): ?Order
    {
        return $this->createQueryBuilder('o')
            ->select('o', 'u', 'oi')
            ->leftJoin('o.user', 'u')
            ->leftJoin('o.orderItems', 'oi')
            ->where('o.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllWithUsers(): array
    {
        return $this->createQueryBuilder('o')
            ->select('o', 'u')
            ->leftJoin('o.user', 'u')
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}