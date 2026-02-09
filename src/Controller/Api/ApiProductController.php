<?php

namespace App\Controller\Api;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Contrôleur API des produits.
 * Fournit l'accès aux données produits au format JSON.
 * Nécessite une authentification JWT.
 */
#[Route('/api')]
class ApiProductController extends AbstractController
{
    /**
     * Retourne la liste de tous les produits au format JSON.
     * Utilise le groupe de sérialisation 'api:product:read'.
     */
    #[Route('/products', name: 'api_products', methods: ['GET'])]
    public function index(
        ProductRepository $productRepository,
        SerializerInterface $serializer
    ): JsonResponse {
        $products = $productRepository->findAll();

        $json = $serializer->serialize($products, 'json', [
            'groups' => ['api:product:read'],
        ]);

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }
}
