<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur du compte utilisateur.
 * Gère l'affichage du compte, l'activation/désactivation de l'accès API
 * et la suppression du compte.
 */
#[Route('/account')]
class AccountController extends AbstractController
{
    /**
     * Affiche la page du compte avec l'historique des commandes validées.
     */
    #[Route('', name: 'app_account')]
    public function index(OrderRepository $orderRepository): Response
    {
        $user = $this->getUser();
        $orders = $orderRepository->findValidatedOrdersByUser($user);

        return $this->render('account/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    /**
     * Active ou désactive l'accès API pour l'utilisateur connecté.
     * Vérifie le token CSRF avant de modifier l'accès.
     */
    #[Route('/toggle-api', name: 'app_account_toggle_api', methods: ['POST'])]
    public function toggleApi(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérification du token CSRF
        if (!$this->isCsrfTokenValid('toggle-api', $request->request->get('_token'))) {
            $this->addFlash('error', 'Action non autorisée.');
            return $this->redirectToRoute('app_account');
        }

        $user = $this->getUser();
        $user->setApiAccess(!$user->isApiAccess());

        $entityManager->flush();

        if ($user->isApiAccess()) {
            $this->addFlash('success', 'Votre accès API a été activé.');
        } else {
            $this->addFlash('success', 'Votre accès API a été désactivé.');
        }

        return $this->redirectToRoute('app_account');
    }

    /**
     * Supprime le compte de l'utilisateur connecté.
     * Déconnecte l'utilisateur, puis supprime toutes ses commandes et son compte.
     */
    #[Route('/delete', name: 'app_account_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        EntityManagerInterface $entityManager,
        Security $security
    ): Response {
        $user = $this->getUser();

        // Check CSRF token
        if (!$this->isCsrfTokenValid('delete-account', $request->request->get('_token'))) {
            $this->addFlash('error', 'Action non autorisée.');
            return $this->redirectToRoute('app_account');
        }

        // Suppression de toutes les commandes et du compte utilisateur
        foreach ($user->getOrders() as $order) {
            foreach ($order->getItems() as $item) {
                $entityManager->remove($item);
            }
            $entityManager->remove($order);
        }
        $entityManager->remove($user);
        $entityManager->flush();

        // Déconnexion après suppression (le flash doit être ajouté avant le logout)
        $this->addFlash('success', 'Votre compte a été supprimé.');
        $security->logout(false);

        return $this->redirectToRoute('app_home');
    }
}
