<?php

namespace CanalTP\SamEcoreSecurityBundle\Form\Type\Role;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\ORM\EntityRepository;
/**
 * Description of ApplicationRoleType
 *
 * @author David Quintanel <david.quintanel@canaltp.fr>
 */
class CopyRoleByApplicationType extends AbstractType
{
    protected $translator;

    public function __construct($translator)
    {
        $this->translator = $translator;
    }

     /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();

                $form->add('role', 'entity', array(
                    'label'         => $this->translator->trans('role.field.copyRole.label') . ' ' . $data->getName(),
                    'multiple'      => false,
                    'expanded'      => true,
                    'class'         => 'CanalTPSamCoreBundle:Role',
                    'query_builder' => function(EntityRepository $er) use ($data) {
                        $qb = $er->createQueryBuilder('r')
                            ->where('r.application = :application')
                            ->setParameter('application', $data->getId())
                            ->orderBy('r.name', 'ASC');

                        return $qb;
                    },
                    'translation_domain' => 'messages',
                    'property' => 'name'
                ));
            }
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CanalTP\SamCoreBundle\Entity\Application'
        ));
    }
}
