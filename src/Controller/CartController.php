<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\User;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Cart controller.
 * Handles cart display, validation and clearing.
 */
#[Route('/cart')]
class CartController extends AbstractController
{
    /**
     * Displays the current user's cart contents.
     */
    #[Route('', name: 'app_cart')]
    public function index(OrderRepository $orderRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $cart = $orderRepository->findCartByUser($user);

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
        ]);
    }

    /**
     * Validates the cart and turns it into an order.
     */
    #[Route('/validate', name: 'app_cart_validate', methods: ['POST'])]
    public function validate(
        Request $request,
        OrderRepository $orderRepository,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$this->isCsrfTokenValid('cart-validate', $request->request->get('_token'))) {
            $this->addFlash('error', 'Action non autorisée.');
            return $this->redirectToRoute('app_cart');
        }

        /** @var User $user */
        $user = $this->getUser();
        $cart = $orderRepository->findCartByUser($user);

        if (!$cart || $cart->getItems()->isEmpty()) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('app_cart');
        }

        $cart->setStatus(Order::STATUS_VALIDATED);
        $cart->setValidatedAt(new \DateTimeImmutable());

        $entityManager->flush();

        $this->addFlash('success', 'Votre commande a été validée avec succès !');

        return $this->redirectToRoute('app_cart');
    }

    /**
     * Clears the user's cart entirely.
     */
    #[Route('/clear', name: 'app_cart_clear', methods: ['POST'])]
    public function clear(
        Request $request,
        OrderRepository $orderRepository,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$this->isCsrfTokenValid('cart-clear', $request->request->get('_token'))) {
            $this->addFlash('error', 'Action non autorisée.');
            return $this->redirectToRoute('app_cart');
        }

        /** @var User $user */
        $user = $this->getUser();
        $cart = $orderRepository->findCartByUser($user);

        if ($cart) {
            $entityManager->remove($cart);
            $entityManager->flush();

            $this->addFlash('success', 'Votre panier a été vidé.');
        }

        return $this->redirectToRoute('app_cart');
    }
}
