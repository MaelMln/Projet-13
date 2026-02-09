<?php

namespace App\DataFixtures;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Main application fixtures.
 * Creates a test user and 5 validated orders with products.
 * Depends on ProductFixtures.
 */
class AppFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('client@greengoodies.fr');
        $user->setFirstName('Marie');
        $user->setLastName('Dupont');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
        $manager->persist($user);
        $manager->flush();

        $products = $manager->getRepository(Product::class)->findAll();

        $dates = [
            new \DateTimeImmutable('2024-01-01'),
            new \DateTimeImmutable('2024-01-01'),
            new \DateTimeImmutable('2024-01-01'),
            new \DateTimeImmutable('2024-01-01'),
            new \DateTimeImmutable('2024-01-01'),
        ];

        foreach ($dates as $date) {
            $order = new Order();
            $order->setUser($user);
            $order->setStatus(Order::STATUS_VALIDATED);
            $order->setValidatedAt($date);

            $item1 = new OrderItem();
            $item1->setProduct($products[0]); // Kit couvert 12.30€
            $item1->setQuantity(3);
            $item1->setUnitPrice($products[0]->getPrice());
            $order->addItem($item1);

            $item2 = new OrderItem();
            $item2->setProduct($products[2]); // Savon Bio 18.90€
            $item2->setQuantity(2);
            $item2->setUnitPrice($products[2]->getPrice());
            $order->addItem($item2);

            $item3 = new OrderItem();
            $item3->setProduct($products[4]); // Shot Tropical 4.50€
            $item3->setQuantity(5);
            $item3->setUnitPrice($products[4]->getPrice());
            $order->addItem($item3);

            $manager->persist($order);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProductFixtures::class,
        ];
    }
}
