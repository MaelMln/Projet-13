<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Vérifie que l'utilisateur authentifié a bien l'accès API activé
 * sur toutes les routes /api (sauf /api/login).
 */
class ApiAccessSubscriber implements EventSubscriberInterface
{
    public function __construct(private Security $security)
    {
    }

    public static function getSubscribedEvents(): array
    {
        // Priorité basse pour s'exécuter après l'authentification JWT du firewall
        return [
            KernelEvents::REQUEST => ['onKernelRequest', -10],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $path = $request->getPathInfo();

        // Ne concerne que les routes /api sauf /api/login
        if (!str_starts_with($path, '/api') || $path === '/api/login') {
            return;
        }

        $user = $this->security->getUser();

        if (!$user instanceof User || !$user->isApiAccess()) {
            $event->setResponse(new JsonResponse(
                ['error' => 'API access not enabled'],
                Response::HTTP_FORBIDDEN
            ));
        }
    }
}
