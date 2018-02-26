<?php

namespace CanalTP\SamEcoreSecurityBundle\Form\Type\Role;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Description of RoleType
 *
 * @author akambi <contact@akambi-fagbohoun.com>
 */
class RoleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            null,
            array(
                'constraints' => array(
                        new NotBlank(),
                        new Length(array('min' => 3, 'max' => 255))
                )
            )
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CanalTP\SamCoreBundle\Entity\Role'
        ));
    }
}
