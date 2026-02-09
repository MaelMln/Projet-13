<?php

namespace App\Controller\Api;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * API products controller.
 * Provides product data as JSON. Requires JWT authentication.
 */
#[Route('/api')]
class ApiProductController extends AbstractController
{
    /**
     * Returns all products as JSON using the 'api:product:read' serialization group.
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
