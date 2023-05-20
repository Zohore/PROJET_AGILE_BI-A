<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;
use const true;

class BcclientType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, array(


                'label' => 'N° de bon de commande',
                'required' => false,
                'attr' => array()
            ))
            ->add('ncontrat', TextType::class, array(


                'label' => 'N° de Contrat cadre',
                'required' => false,
                'attr' => array()
            ))->add('application')
            ->add('avenant')
            ->add('type', ChoiceType::class, array(
                'choices' => array(

                    'DIRECT' => 'DIRECT',
                    'PORTAGE' => 'PORTAGE',


                ),
            ))
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',

                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => false,

                // adds a class that can be selected in JavaScript
                'attr' => ['class' => 'date-timepicker1'],
            ])
            ->add('client', EntityType::class, array(
                'class' => 'AppBundle:Client',
                'multiple' => false,
                'placeholder' => '--',

                'label' => 'Client',
                'required' => false,
                'attr' => array(
                    'class' => 'chosen-select',
                    'data-placeholder' => 'Selectionner',
                    'multiple' => false

                )
            ))->add('mission', EntityType::class, array(
                'class' => 'AppBundle:Mission',
                'multiple' => false,
                'placeholder' => '--',

                'label' => 'Mission',
                'required' => false,
                'attr' => array(
                    'class' => 'chosen-select',
                    'data-placeholder' => 'Selectionner',
                    'multiple' => false

                )
            ))->add('projet', EntityType::class, array(
                'class' => 'AppBundle:Projet',
                'multiple' => false,
                'placeholder' => '--',

                'label' => 'Projet',
                'required' => false,
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
                'placeholder' => '--',

                'required' => false,
                'attr' => array(
                    'class' => 'chosen-select',
                    'data-placeholder' => 'Selectionner',
                    'multiple' => false

                )
            ))

            ->add('nbJrs', NumberType::class, [
                'required'   => true,

            ])
            ->add('bcFile', VichFileType::class, [
                'required' => false,
                'allow_delete' => true,
                'label' => 'Bon de commande client'
                //   'delete_label' => 'form.label.delete',

            ])
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Bcclient'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_bcclient';
    }


}
