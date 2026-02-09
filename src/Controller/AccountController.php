<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * User account controller.
 * Handles account display, API access toggle and account deletion.
 */
#[Route('/account')]
class AccountController extends AbstractController
{
    /**
     * Displays the account page with validated order history.
     */
    #[Route('', name: 'app_account')]
    public function index(OrderRepository $orderRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $orders = $orderRepository->findValidatedOrdersByUser($user);

        return $this->render('account/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    /**
     * Toggles API access for the current user.
     */
    #[Route('/toggle-api', name: 'app_account_toggle_api', methods: ['POST'])]
    public function toggleApi(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('toggle-api', $request->request->get('_token'))) {
            $this->addFlash('error', 'Action non autorisée.');
            return $this->redirectToRoute('app_account');
        }

        /** @var User $user */
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
     * Deletes the current user's account and logs them out.
     */
    #[Route('/delete', name: 'app_account_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        EntityManagerInterface $entityManager,
        Security $security
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        if (!$this->isCsrfTokenValid('delete-account', $request->request->get('_token'))) {
            $this->addFlash('error', 'Action non autorisée.');
            return $this->redirectToRoute('app_account');
        }

        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'Votre compte a été supprimé.');
        $security->logout(false);

        return $this->redirectToRoute('app_home');
    }
}
