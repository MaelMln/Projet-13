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
 * Contrôleur du panier.
 * Gère l'affichage, la validation et le vidage du panier de l'utilisateur.
 */
#[Route('/cart')]
class CartController extends AbstractController
{
    /**
     * Affiche le contenu du panier de l'utilisateur connecté.
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
     * Valide le panier en cours et le transforme en commande.
     * Vérifie le token CSRF et que le panier n'est pas vide.
     */
    #[Route('/validate', name: 'app_cart_validate', methods: ['POST'])]
    public function validate(
        Request $request,
        OrderRepository $orderRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Vérification du token CSRF
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

        // Validate the order
        $cart->setStatus(Order::STATUS_VALIDATED);
        $cart->setValidatedAt(new \DateTimeImmutable());

        $entityManager->flush();

        $this->addFlash('success', 'Votre commande a été validée avec succès !');

        return $this->redirectToRoute('app_cart');
    }

    /**
     * Vide entièrement le panier de l'utilisateur.
     * Supprime tous les items et la commande associée.
     */
    #[Route('/clear', name: 'app_cart_clear', methods: ['POST'])]
    public function clear(
        Request $request,
        OrderRepository $orderRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Vérification du token CSRF
        if (!$this->isCsrfTokenValid('cart-clear', $request->request->get('_token'))) {
            $this->addFlash('error', 'Action non autorisée.');
            return $this->redirectToRoute('app_cart');
        }

        /** @var User $user */
        $user = $this->getUser();
        $cart = $orderRepository->findCartByUser($user);

        if ($cart) {
            // Remove all items
            foreach ($cart->getItems() as $item) {
                $entityManager->remove($item);
            }
            $cart->clearItems();

            // Remove the cart itself
            $entityManager->remove($cart);
            $entityManager->flush();

            $this->addFlash('success', 'Votre panier a été vidé.');
        }

        return $this->redirectToRoute('app_cart');
    }
}
