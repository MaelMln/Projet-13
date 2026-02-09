<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Order repository.
 * Provides custom queries for orders (cart, validated orders).
 *
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * Finds the current cart for a user.
     */
    public function findCartByUser(User $user): ?Order
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.user = :user')
            ->andWhere('o.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', Order::STATUS_CART)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Finds validated orders for a user, sorted by date descending.
     *
     * @return Order[]
     */
    public function findValidatedOrdersByUser(User $user): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.user = :user')
            ->andWhere('o.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', Order::STATUS_VALIDATED)
            ->orderBy('o.validatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
