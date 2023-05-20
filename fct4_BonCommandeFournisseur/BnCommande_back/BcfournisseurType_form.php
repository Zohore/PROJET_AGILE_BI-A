<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class BcfournisseurType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nbjours')
            ->add('mois', ChoiceType::class, array(
                'placeholder' => 'Choose an option',
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
            ->add('year')
            ->add('achatTTC')
            ->add('achatHT')
            ->add('taxe')
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,

                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => false,

                // adds a class that can be selected in JavaScript
                'attr' => ['class' => 'date-timepicker1'],
            ])
            //   ->add('documentName')
            /*  ->add('documentFile', VichFileType::class, [
                  'required' => false,
                  'allow_delete' => true,
                  'label' => 'Facture fournisseur'
                  //   'delete_label' => 'form.label.delete',

              ])*/
            ->add('fournisseur', EntityType::class, array(
                'class' => 'AppBundle:Fournisseur',
                'multiple' => false,
                'label' => 'Fournisseur',
                'placeholder' => 'Selectionner',
                'required' => false,

                'attr' => array(
                    'class' => 'chosen-select',
                    'data-placeholder' => 'Selectionner',
                    'multiple' => false

                )
            ))
            ->add('facture', EntityType::class, array(
                'class' => 'AppBundle:Facture',
                'multiple' => false,
                'label' => 'Facture',
                'placeholder' => 'Selectionner',
                'required' => true,

                'attr' => array(
                    'class' => 'chosen-select',
                    'data-placeholder' => 'Selectionner',
                    'multiple' => false

                )
            ))
            ->add('consultant', EntityType::class, array(
                'class' => 'AppBundle:Consultant',
                'multiple' => false,
                'label' => 'Consultant',
                'placeholder' => 'Selectionner',
                'required' => false,

                'attr' => array(
                    'class' => 'chosen-select',
                    'data-placeholder' => 'Selectionner',
                    'multiple' => false

                )
            ))
            ->add('mission', EntityType::class, array(
                'class' => 'AppBundle:Mission',
                'multiple' => false,
                'label' => 'Mission',
                'placeholder' => 'Selectionner',
                'required' => false,
                'attr' => array(
                    'class' => 'chosen-select',
                    'data-placeholder' => 'Selectionner',
                    'multiple' => false

                )
            ))->add('projet', EntityType::class, array(
                'class' => 'AppBundle:Projet',
                'multiple' => false,
                'label' => 'Projet',
                'required' => false,

                'placeholder' => 'Selectionner',

                'attr' => array(
                    'class' => 'chosen-select',
                    'data-placeholder' => 'Selectionner',
                    'multiple' => false

                )
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Bcfournisseur'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_bcfournisseur';
    }


}
