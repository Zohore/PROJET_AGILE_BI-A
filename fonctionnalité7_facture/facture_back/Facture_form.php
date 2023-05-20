<?php

namespace AppBundle\Form;

use AppBundle\Entity\LigneFacture;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


class Facture2Type extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numero')
            ->add('mois', ChoiceType::class, array(
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'choices' => array(
                    'Janvier' => 1,
                    'Fevrier' => 2,
                    'Mars' => 3,
                    'Avril' => 4,
                    'Mai' => 5,
                    'Juin' => 6,
                    'Juillet' => 7,
                    'Aout' => 8,
                    'Septembre' => 9,
                    'Octobre' => 10,
                    'Novembre' => 11,
                    'Décembre' => 12,


                ),
            ))
            ->add('year',NumberType::class,array(

                'required'=>true,
            ))
            ->add('projet', EntityType::class, array(
                'class' => 'AppBundle:Projet',
                'multiple' => false,
                'label' => 'Projet',

                'attr' => array(
                    'class' => 'chosen-select',
                    'data-placeholder' => 'Selectionner',
                    'multiple' => false

                )
            )) ->add('projet', EntityType::class, array(
                'class' => 'AppBundle:Projet',
                'multiple' => false,
                'label' => 'Projet',

                'attr' => array(
                    'class' => 'chosen-select',
                    'data-placeholder' => 'Selectionner',
                    'multiple' => false

                )
            ))
            ->add('client', EntityType::class, array(
                'class' => 'AppBundle:Client',
                'multiple' => false,
                'label' => 'Client',
                'attr' => array(
                    'class' => 'chosen-select',
                    'data-placeholder' => 'Selectionner',
                    'multiple' => false

                )
            ))
            ->add('dateDebut', DateTimeType::class, [
                'widget' => 'single_text',
                'required'=>false,
                'placeholder' => 'Date Début',
                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => false,

                // adds a class that can be selected in JavaScript
                'attr' => ['class' => 'date-timepicker1'],
            ])
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
                'placeholder' => 'Date Facture',
                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => false,

                // adds a class that can be selected in JavaScript
                'attr' => ['class' => 'date-timepicker1'],
            ])
            ->add('dateFin', DateTimeType::class, [
                'widget' => 'single_text',
                'required'=>false,
                'placeholder' => 'Date Fin',
                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => false,

                // adds a class that can be selected in JavaScript
                'attr' => ['class' => 'date-timepicker1'],
            ])
            ->add('comptebancaire', EntityType::class, array(
                'class' => 'AppBundle:Comptebancaire',
                'multiple' => false,
                'label' => 'Compte bancaire',
                'attr' => array(
                    'class' => 'chosen-select',
                    'data-placeholder' => 'Selectionner',
                    'multiple' => false

                )
            ))
            ->add('lignes',CollectionType::class,array('entry_type'=>LigneFactureType::class,
                'entry_options'=>['label'=>false],
                'attr'=>[
                    'class'=>'my-selector inl',
                    'label'=>'consultants list'
                ]
                ))
            ->add('lignes', CollectionType::class, [
                'entry_type' => LigneFactureType::class,

                'entry_options' => ['label' => false],
                'attr' => array(
                    'class' => 'my-selector inl ',
                    'label' => 'consultants list :',
                ),
                'prototype' => true,
                'allow_add' => false,
                'allow_delete' => false,

                'by_reference' => false,

            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Facture'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_facture';
    }


}
