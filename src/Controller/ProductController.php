<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Entity\User;
use App\Form\AddToCartType;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Product page controller.
 * Handles product detail display and cart management.
 */
#[Route('/product')]
class ProductController extends AbstractController
{
    /**
     * Displays product detail and handles cart add/update.
     * A quantity of 0 removes the product from the cart.
     */
    #[Route('/{id}', name: 'app_product_show', methods: ['GET', 'POST'])]
    public function show(
        Product $product,
        Request $request,
        OrderRepository $orderRepository,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var User|null $user */
        $user = $this->getUser();
        $cart = null;
        $existingItem = null;
        $currentQuantity = 0;

        if ($user) {
            $cart = $orderRepository->findCartByUser($user);
            if ($cart) {
                $existingItem = $cart->getItemForProduct($product);
                if ($existingItem) {
                    $currentQuantity = $existingItem->getQuantity();
                }
            }
        }

        $form = $this->createForm(AddToCartType::class, null, [
            'current_quantity' => $currentQuantity,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $user) {
            $quantity = $form->get('quantity')->getData();

            if (!$cart) {
                $cart = new Order();
                $cart->setUser($user);
                $cart->setStatus(Order::STATUS_CART);
                $entityManager->persist($cart);
            }

            if ($quantity > 0) {
                if ($existingItem) {
                    $existingItem->setQuantity($quantity);
                } else {
                    $item = new OrderItem();
                    $item->setProduct($product);
                    $item->setQuantity($quantity);
                    $item->setUnitPrice($product->getPrice() ?? 0);
                    $cart->addItem($item);
                }
            } else {
                if ($existingItem) {
                    $cart->removeItem($existingItem);
                    $entityManager->remove($existingItem);
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Panier mis à jour.');

            return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'form' => $form,
            'in_cart' => $existingItem !== null,
            'current_quantity' => $currentQuantity,
        ]);
    }
}
