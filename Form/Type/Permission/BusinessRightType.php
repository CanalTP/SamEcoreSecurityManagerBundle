<?php

namespace CanalTP\SamEcoreSecurityBundle\Form\Type\Permission;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
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
                'choice_list' => new ObjectChoiceList($permissions, 'name', array(), null, 'id'),
            )//,https://github.com/symfony/symfony/pull/10309
//            array(
//                'choices_attr' => function ($choice) {
//                    debug($choice);die();
//            
//                    $attributes = array();
//
//                    $attributes['disabled'] = true;
//
//                    return $attributes;    
//                }
//            )
        );

        // Change Permissions in PermissionInterface[]
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($permissions) {
                $data = $event->getData();
//                if (!$data->getIsEditable()) {
//                    return null;
//                }
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
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CanalTP\SamCoreBundle\Entity\Role',
        ));
        
        $resolver->setRequired(array('application'));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sam_business_right';
    }
}
