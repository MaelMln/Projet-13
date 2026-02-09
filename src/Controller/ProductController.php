<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Form\AddToCartType;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur de la page produit.
 * Gère l'affichage du détail d'un produit et l'ajout au panier.
 */
#[Route('/product')]
class ProductController extends AbstractController
{
    /**
     * Affiche le détail d'un produit et gère l'ajout/mise à jour du panier.
     * Si l'utilisateur est connecté, le formulaire permet d'ajouter ou modifier la quantité.
     * Une quantité de 0 retire le produit du panier.
     */
    #[Route('/{id}', name: 'app_product_show', methods: ['GET', 'POST'])]
    public function show(
        Product $product,
        Request $request,
        OrderRepository $orderRepository,
        EntityManagerInterface $entityManager
    ): Response {
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

            // Create cart if doesn't exist
            if (!$cart) {
                $cart = new Order();
                $cart->setUser($user);
                $cart->setStatus(Order::STATUS_CART);
                $entityManager->persist($cart);
            }

            if ($quantity > 0) {
                if ($existingItem) {
                    // Update existing item
                    $existingItem->setQuantity($quantity);
                } else {
                    // Add new item
                    $item = new OrderItem();
                    $item->setProduct($product);
                    $item->setQuantity($quantity);
                    $item->setUnitPrice($product->getPrice());
                    $cart->addItem($item);
                }
            } else {
                // Remove item if quantity is 0
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
