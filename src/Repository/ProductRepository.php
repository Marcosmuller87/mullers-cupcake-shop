<?php
namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findAllActive()
    {
        return $this->createQueryBuilder('p')
            ->select('p.id, p.name, p.description, p.price, p.image, p.isActive')
            ->where('p.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllIncludingDeleted()
    {
        return $this->createQueryBuilder('p')
            ->select('p.id, p.name, p.description, p.price, p.image, p.isActive')
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findOneWithoutDeleted(int $id)
    {
        return $this->createQueryBuilder('p')
            ->select('p.id, p.name, p.description, p.price, p.image, p.isActive')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}