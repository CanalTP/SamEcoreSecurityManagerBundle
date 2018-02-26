<?php

namespace CanalTP\SamEcoreSecurityBundle\Form\Type\Role;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use CanalTP\SamEcoreSecurityBundle\Form\Type\Role\RoleType;

/**
 * Description of RoleType
 *
 * @author David Quintanel <david.quintanel@canaltp.fr>
 */
class CreateRoleType extends AbstractType
{
    protected $roleListener;

    public function __construct($roleListener)
    {
        $this->roleListener = $roleListener;
    }

     /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('role', RoleType::class);

        $builder->addEventSubscriber($this->roleListener);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CanalTP\SamEcoreSecurityBundle\Form\Model\RegistrationRole'
        ));
    }
}
