<?php

namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class,[
                'label' => "Type*",
                'attr' => ['required' => true],
                'choices' => [
                    'Satisfaction' => 'satisfaction',
                    'Evolution' => 'evolution',
                    'Aide' => 'support',
                    'Anomalie' => 'anomalie',
                    'Autre' => 'autre',
                ],
            ])
            ->add('content', TextareaType::class,[
                'label' => "Message*",
                'attr' => ['required' => true, 'rows' => 10, 'cols'=> 40],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
