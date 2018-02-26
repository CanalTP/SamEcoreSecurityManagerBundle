<?php

namespace CanalTP\SamEcoreSecurityBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use CanalTP\SamEcoreSecurityBundle\Form\Type\Permission\ApplicationRoleType;
use CanalTP\SamEcoreSecurityBundle\Form\Type\Permission\BusinessRightType;

class BusinessRightController extends Controller
{
    private function processForm(Request $request, $form)
    {
        $form->handleRequest($request);

        if ($form->isValid()) {
            $msg = $this->get('translator')->trans('message.notice.business_right.edit');
            $em = $this->getDoctrine()->getManager();

            $em->persist($form->getData());
            $em->flush();
            $this->get('session')->getFlashBag()->set('success', $msg);

            return true;
        }

        return false;
    }

    private function buildForm($application)
    {
        $form = $this->createForm(
            ApplicationRoleType::class,
            $application,
            array(
                'action' => $this->generateUrl(
                    'sam_security_business_right_edit',
                    array('appId' => $application->getId())
                )
            )
        );

        return ($form);
    }

    public function editAction(Request $request)
    {
        if (null === $appId = $request->query->get('appId')) {
            $appId = $this->get('canal_tp_sam.application.finder')->findFromUrl()->getId();
        }

        $em = $this->getDoctrine()->getManager();
        $application = $em->getRepository('CanalTPSamCoreBundle:Application')
            ->findWithEditableRoles($appId);

        if (!$application) {
            throw new LogicException(sprintf("Application with id (%d) not found", $appId));
        }

        $form = $this->buildForm($application);
        $render = $this->processForm($request, $form);

        if ($render) {
            $form = $this->buildForm($application);
        }

        return $this->render(
            'CanalTPSamEcoreSecurityBundle:BusinessRight:edit.html.twig',
            array(
                'form'         => $form->createView(),
                'applications' => $em->getRepository('CanalTPSamCoreBundle:Application')->findAll(),
                'appId'        => $application->getId()
            )
        );
    }
}
