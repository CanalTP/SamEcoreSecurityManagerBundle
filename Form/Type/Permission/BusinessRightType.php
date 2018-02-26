<?php

namespace CanalTP\SamEcoreSecurityBundle\Form\Type\Permission;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use CanalTP\SamEcoreApplicationManagerBundle\Component\BusinessComponentRegistry;
use CanalTP\SamEcoreApplicationManagerBundle\Permission\BusinessPermission;

class BusinessRightType extends AbstractType
{
    private $businessComponentRegistry;
    protected $securityContext;

    public function __construct(BusinessComponentRegistry $businessComponentRegistry, $securityContext)
    {
        $this->businessComponentRegistry = $businessComponentRegistry;
        $this->securityContext = $securityContext;
    }

     /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $permissions = $this->businessComponentRegistry
            ->getBusinessComponent($options['application']->getCanonicalName())
            ->getPermissionsManager()
            ->getBusinessModules()
            ->getPermissions();

        $disabled = !$this->securityContext->isGranted('BUSINESS_MANAGE_PERMISSION');

        $builder->add(
            'businessPermissions',
            'choice',
            array(
                'label'       => 'role.field.application',
                'multiple'    => true,
                'expanded'    => true,
                'required'    => false,
                'disabled'    => $disabled,
                'choices' => $permissions,
                'choices_as_values' => true,
                'choice_label' => function ($permission, $key, $index) {
                    return $permission->getName();
                },
            )
        );

        // Change Permissions in PermissionInterface[]
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($permissions) {
                $data = $event->getData();
                $permissionsArray = array();
                foreach ($data->getPermissions() as $key => $permission) {
                    $model = new BusinessPermission();
                    foreach ($permissions as $perm) {
                        if ($perm->getId() === $permission) {
                            $model = $perm;
                            break;
                        }
                    }

                    $permissionsArray[] = $model;
                }
                $data->setBusinessPermissions($permissionsArray);
            }
        );

        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) use ($permissions) {
                $data = $event->getData();

                $permissionsArray = array();
                foreach ($data->getBusinessPermissions() as $key => $permission) {
                    $permissionsArray[] = $permission->getId();
                }
                $data->setPermissions($permissionsArray);
            }
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CanalTP\SamCoreBundle\Entity\Role',
        ));

        $resolver->setRequired(array('application'));
    }
}
