<?php

namespace CanalTP\SamEcoreSecurityBundle\Form\Type\Permission;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use CanalTP\SamEcoreSecurityBundle\Form\Type\Permission\BusinessRightType;

class ApplicationRoleType extends AbstractType
{
     /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('roles', 'collection',
            array(
                'type' => BusinessRightType::class,
                'options' => array('application' => $builder->getData()),
            )
        );

        $builder->setAction($options['action']);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CanalTP\SamCoreBundle\Entity\Application',
            'attr' => array('novalidate' => 'novalidate')
        ));
    }
}
