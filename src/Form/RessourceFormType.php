<?php

namespace App\Form;

use App\Entity\FichePerso;
use App\Entity\Ressource;
use Doctrine\DBAL\Types\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RessourceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', null,[
                'label' => 'Nom de la ressource : ',
            ])
            ->add('rangeMax',null,[
                'label' => 'Valeur max : ',
            ])
            ->add('fichePerso', EntityType::class,[
                'class' => FichePerso::class,
                'attr' => ['type' => 'hidden']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ressource::class,
        ]);
    }
}
