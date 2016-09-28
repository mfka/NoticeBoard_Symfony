<?php

namespace AppBundle\Form;

use AppBundle\AppBundle;
use AppBundle\Entity\Notice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CommentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', 'hidden')
            ->add('text', 'textarea')
//            ->add('user', 'hidden', ['data' => 'AppBundle:User'])
//            ->add('notices', 'entity', [
//                'class' => 'AppBundle:Notice',
//            ])
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Comment'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_comment';
    }
}
