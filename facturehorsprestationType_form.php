<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class facturehorsprestationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nbjours')
            ->add('mois', ChoiceType::class, array(
                'choices' => array(
                    '--' => null,
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


                ), 'attr' => array(
                    'class' => 'chosen-select',
                    'data-placeholder' => 'Selectionner',
                    'multiple' => false,
                    'required' => true

                )
            ))
            ->add('numero')
            ->add('nbjours')
            ->add('achatHT', NumberType::class, array(
                'required' => true,
                'label' => 'Achat HT',
                'attr' => [
                    'class' => 'achatHT'
                ]
            ))->add('achatTTC', NumberType::class, array(
                'required' => true,
                'label' => 'Achat TTC',

                'attr' => [
                    'class' => 'achatTTC'
                ]
            ))
            ->add('fournisseur', EntityType::class, array(
                'class' => 'AppBundle:Fournisseur',
                'multiple' => false,
                'label' => 'Fournisseur',
                'required' => true,
                'placeholder' => '',
                'attr' => array(
                    'class' => 'chosen-select',
                    'data-placeholder' => 'Selectionner un fournisseur',
                    'multiple' => false

                )))
            ->add('taxe', NumberType::class, array(
                'required' => true,
                'label' => 'Taxe',
                'attr' => [
                    'class' => 'taxe'
                ]
            ))
            ->add('year', NumberType::class, array(
                'required' => true
            ))
            ->add('etat', ChoiceType::class, array(
                'choices' => array(
                    'Payé' => 'payé',
                    'Payé avec devise' => 'payé avec devise',
                    'Non Payé' => 'non payé',
                ),
            ))
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
                'placeholder' => 'Date Facture',
                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => false,

                // adds a class that can be selected in JavaScript
                'attr' => ['class' => 'date-timepicker1'],
            ])
            ->add('documentFile', VichFileType::class, [
                'required' => true,
                'allow_delete' => true,
                'label' => 'Facture hors prestation'
                //   'delete_label' => 'form.label.delete',

            ])
        
        ->add('designation');
      
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\facturehorsprestation'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_facturehorsprestation';
    }


}
