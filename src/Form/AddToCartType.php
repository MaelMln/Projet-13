<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

/**
 * Add-to-cart form.
 * Allows entering a quantity for a product.
 * The submit button label adapts based on whether the product is already in the cart.
 */
class AddToCartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentQuantity = $options['current_quantity'];

        $builder
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantité',
                'data' => $currentQuantity > 0 ? $currentQuantity : 1,
                'attr' => [
                    'min' => 0,
                    'class' => 'form__input',
                ],
                'constraints' => [
                    new PositiveOrZero(),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => $currentQuantity > 0 ? 'Mettre à jour' : 'Ajouter au panier',
                'attr' => [
                    'class' => 'btn btn--secondary btn--full-width',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'current_quantity' => 0,
        ]);
    }
}
