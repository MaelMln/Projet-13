<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour l'entité Order.
 * Fournit les requêtes personnalisées pour les commandes (panier, commandes validées).
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
     * Find the current cart for a user
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
     * Find validated orders for a user
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
