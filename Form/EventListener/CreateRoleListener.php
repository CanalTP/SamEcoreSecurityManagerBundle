<?php

/**
 * Description of RoleListener
 *
 * @author David Quintanel <david.quintanel@canaltp.fr>
 */

namespace CanalTP\SamEcoreSecurityBundle\Form\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Validator\Constraints\NotBlank;

use CanalTP\SamCoreBundle\Entity\Role;
use CanalTP\SamCoreBundle\Entity\UserApplicationRole;
use CanalTP\SamCoreBundle\Entity\Application;
use CanalTP\SamCoreBundle\CanalTPSamCoreBundle;
use CanalTP\SamEcoreSecurityBundle\Form\Type\Role\CopyRoleByApplicationType;

class CreateRoleListener implements EventSubscriberInterface
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData'
        );
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $applications = $this->em->getRepository('CanalTPSamCoreBundle:Application')->findAllOrderedByName();
        $data->rolesByApplication = $applications;

        $form->add(
            'applications',
            ChoiceType::class,
            array(
                'label'             => 'role.field.application',
                'multiple'          => true,
                'expanded'          => true,
                'required'          => false,
                'choices'           => $applications,
                'choices_as_values' => true,
                'choice_label' => function ($app, $key, $index) {
                    return $app->getName();
                },
                'constraints'       => array(new NotBlank())
            )
        );

        $form->add(
            'rolesByApplication',
            'collection',
            array(
                'label'        => 'role.field.parent.label',
                'type'         => CopyRoleByApplicationType::class,
                'allow_add'    => false,
                'allow_delete' => false,
                'by_reference' => false,
                'options'      => array(
                    'required'       => true,
                    'error_bubbling' => false,
                    'attr'           => array('class' => 'application-role-box')
                ),
            )
        );
    }
}
