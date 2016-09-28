<?php

namespace AppBundle\Form;

use AppBundle\AppBundle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NoticeType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('title')
                ->add('description')
                ->add('photo', 'file', array(
                    'label' => 'Photo (files: .jpg, .png)',
                    'data_class' => null,
                    'required' => false
                    ))
                ->add('status', 'choice', [
                    'choices' => [
                        'Active' => 'Active',
                        'Non active' => 'Not Active',
                    ],
                    'choices_as_values' => true,
                ])

                ->add('categories')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Notice'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'appbundle_notice';
    }

}
