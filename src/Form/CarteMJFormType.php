<?php

namespace App\Form;

use App\Entity\CarteMJ;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class CarteMJFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class,[
                'label' => 'Nom : ',
                'required' => true
            ])
            ->add('type', ChoiceType::class,[
                'label' => 'Type de carte : ',
                'choices' => [
                    'Bestiaire' => 'bestiaire',
                    'PNJ' => 'PNJ'
                ]
            ])
            ->add('filtre', TextType::class,[
                'label' => 'Mot clé : ',
                'required' => false
            ])
            ->add('PV', IntegerType::class,[
                'label' => 'Points de vie : ',
                'required' => true,
                'attr' => ['value'=>1, 'min'=>1, 'max'=>1000]
            ])
            ->add('note', TextareaType::class,[
                'label' => 'Notes : ',
                'required' => false,
                'attr' => ['style' => 'height: 200px']
            ])
            ->add('image', FileType::class,[
                'label' => 'image .jpg (taille max: 150x150) : ',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '15k',
                        'maxHeight' => '150',
                        'maxWidth' => '150',
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CarteMJ::class,
        ]);
    }
}
