<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Fixtures des produits.
 * Insère les 6 produits du catalogue GreenGoodies en base de données.
 */
class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $products = [
            [
                'name' => 'Kit couvert en bois',
                'shortDescription' => 'Revêtement Bio en olivier & sac de transport',
                'fullDescription' => 'Ce kit de couverts en bois comprend une fourchette, un couteau et une cuillère, le tout rangé dans une pochette en tissu pratique. Fabriqués à partir de bois de hêtre certifié FSC, ces couverts sont durables, légers et parfaits pour vos repas à emporter. Lavables et réutilisables, ils constituent une alternative écologique aux couverts jetables en plastique. Idéal pour le bureau, les pique-niques ou les voyages.',
                'price' => 12.30,
                'picture' => 'kit-couvert-bois.jpg',
            ],
            [
                'name' => 'Nécessaire, déodorant Bio',
                'shortDescription' => '50ml déodorant à l’eucalyptus',
				'fullDescription' => "Déodorant Nécessaire, une formule révolutionnaire composée exclusivement d'ingrédients naturels pour une protection efficace et bienfaisante.
				Chaque flacon de 50 ml renferme le secret d'une fraîcheur longue durée, sans compromettre votre bien-être ni l'environnement. Conçu avec soin, ce déodorant allie le pouvoir antibactérien des extraits de plantes aux vertus apaisantes des huiles essentielles, assurant une sensation de confort toute la journée.
				Grâce à sa formule non irritante et respectueuse de votre peau, Nécessaire offre une alternative saine aux déodorants conventionnels, tout en préservant l'équilibre naturel de votre corps.",
				'price' => 8.50,
                'picture' => 'deodorant-bio.jpg',
            ],
            [
                'name' => 'Savon Bio',
                'shortDescription' => 'Thé, Orange & Girofle',
                'fullDescription' => 'Notre savon bio est fabriqué artisanalement selon la méthode traditionnelle de saponification à froid, qui préserve les propriétés des huiles végétales utilisées. Enrichi en beurre de karité bio et en huile d\'olive vierge, il nettoie en douceur tout en nourrissant la peau. Son parfum délicat aux huiles essentielles naturelles procure un moment de bien-être à chaque utilisation. Convient à toute la famille.',
                'price' => 18.90,
                'picture' => 'savon-bio.jpg',
            ],
            [
                'name' => 'Kit Hygiène Recyclable',
                'shortDescription' => 'Pour une salle de bain éco-friendly',
                'fullDescription' => 'Ce kit hygiène recyclable contient tout ce dont vous avez besoin pour une routine quotidienne éco-responsable. Composé de produits naturels et d\'accessoires réutilisables, il vous permet de réduire significativement vos déchets dans la salle de bain. Chaque élément a été soigneusement sélectionné pour sa qualité et son faible impact environnemental. Un premier pas idéal vers le zéro déchet.',
                'price' => 24.99,
                'picture' => 'kit-hygiene-recyclable.jpg',
            ],
            [
                'name' => 'Shot Tropical',
                'shortDescription' => 'Fruits frais, pressés à froid',
                'fullDescription' => 'Notre Shot Tropical est une boisson concentrée à base de fruits exotiques biologiques. Riche en vitamines et antioxydants naturels, ce shot vous apporte un boost d\'énergie sain pour bien démarrer la journée. Sans sucres ajoutés, sans conservateurs, uniquement le meilleur des fruits pressés à froid pour préserver tous leurs bienfaits. À consommer frais pour un maximum de fraîcheur.',
                'price' => 4.50,
                'picture' => 'shot-tropical.jpg',
            ],
            [
                'name' => 'Gourde en Bois',
                'shortDescription' => '50cl, bois d’olivier',
                'fullDescription' => 'Cette gourde en bois allie élégance et fonctionnalité. Son revêtement extérieur en bois véritable lui confère un aspect unique et chaleureux, tandis que son intérieur en acier inoxydable double paroi maintient vos boissons chaudes pendant 12h ou fraîches pendant 24h. Capacité de 500ml, elle vous accompagne partout : au bureau, en randonnée ou au quotidien. Une alternative durable aux bouteilles plastiques.',
                'price' => 16.90,
                'picture' => 'gourde-en-bois.jpg',
            ],
        ];

        foreach ($products as $productData) {
            $product = new Product();
            $product->setName($productData['name']);
            $product->setShortDescription($productData['shortDescription']);
            $product->setFullDescription($productData['fullDescription']);
            $product->setPrice($productData['price']);
            $product->setPicture($productData['picture']);

            $manager->persist($product);
        }

        $manager->flush();
    }
}
