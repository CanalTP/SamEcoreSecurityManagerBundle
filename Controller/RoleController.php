<?php

namespace CanalTP\SamEcoreSecurityBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use CanalTP\SamCoreBundle\Controller\AbstractController;

use CanalTP\SamCoreBundle\Entity\Role;
use CanalTP\SamEcoreSecurityBundle\Form\Model\RegistrationRole;
use CanalTP\SamEcoreSecurityBundle\Form\Type\Role\CreateRoleType;
use CanalTP\SamEcoreSecurityBundle\Form\Type\Role\RoleType;


/**
 * Role controller.
 *
 * @author akambi <akambi.fagbohoun@canaltp.fr>
 */
class RoleController extends AbstractController
{
    /**
     * Lists all Role entities.
     *
     */
    public function indexAction()
    {
        $this->isAllowed('BUSINESS_VIEW_ROLE');

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CanalTPSamCoreBundle:Role')->findByIsEditable(true);

        return $this->render('CanalTPSamEcoreSecurityBundle:Role:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
    * Creates a form to create a Role entity.
    *
    * @param Role $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(RegistrationRole $entity)
    {
        $form = $this->createForm(CreateRoleType::class, $entity, array(
            'action' => $this->generateUrl('sam_role_create'),
            'method' => 'POST'
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Role entity.
     *
     */
    public function newAction()
    {
        $this->isAllowed('BUSINESS_MANAGE_ROLE');

        $entity = new RegistrationRole();
        $form   = $this->createCreateForm($entity);
        return $this->render('CanalTPSamEcoreSecurityBundle:Role:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Role entity.
     *
     */
    public function createAction(Request $request)
    {
        $this->isAllowed('BUSINESS_MANAGE_ROLE');

        $model = new RegistrationRole();
        $form = $this->createCreateForm($model);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $selectedApps = array();
            foreach ($model->applications as $selectedApp) {
                $selectedApps[] = $selectedApp->getId();
            }

            foreach ($model->rolesByApplication as $app) {
                if (in_array($app->getId(), $selectedApps)) {
                    $permissions = null === $app->getRole()
                        ? array()
                        : $app->getRole()->getPermissions();

                    $role = new Role();
                    $role->setName($model->role->getName());
                    $role->setApplication($app);
                    $role->setPermissions($permissions);
                    $em->persist($role);
                }
            }

            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'role.flash.created');

            return $this->redirect($this->generateUrl('sam_role'));
        }

        return $this->render('CanalTPSamEcoreSecurityBundle:Role:new.html.twig', array(
            'entity' => $model,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Role entity.
     *
     */
    public function editAction(Role $role = null)
    {
        $this->isAllowed('BUSINESS_MANAGE_ROLE');

        if (!$role) {
            throw $this->createNotFoundException('Unable to find Role entity.');
        }

        $editForm = $this->createEditForm($role);

        return $this->render('CanalTPSamEcoreSecurityBundle:Role:edit.html.twig', array(
            'entity'      => $role,
            'edit_form'   => $editForm->createView()
        ));
    }

    /**
    * Creates a form to edit a Role entity.
    *
    * @param Role $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Role $entity)
    {
        $form = $this->createForm(RoleType::class, $entity, array(
            'action' => $this->generateUrl('sam_role_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }
    /**
     * Edits an existing Role entity.
     *
     */
    public function updateAction(Request $request, Role $role)
    {
        $this->isAllowed('BUSINESS_MANAGE_ROLE');

        $em = $this->getDoctrine()->getManager();

        if (!$role) {
            throw $this->createNotFoundException('Unable to find Role entity.');
        }

        $editForm = $this->createEditForm($role);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'role.flash.updated');

            return $this->redirect($this->generateUrl('sam_role'));
        }

        return $this->render('CanalTPSamEcoreSecurityBundle:Role:edit.html.twig', array(
            'entity'      => $role,
            'edit_form'   => $editForm->createView()
        ));
    }

    /**
     * Deletes a Role entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $this->isAllowed('BUSINESS_MANAGE_ROLE');

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CanalTPSamCoreBundle:Role')->find($id);

        if ($form->isValid()) {

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Role entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'role.flash.deleted');

            return $this->redirect($this->generateUrl('sam_role'));
        } else {
            return $this->render('CanalTPSamEcoreSecurityBundle:Role:delete.html.twig', array(
                'entity'      => $entity,
                'delete_form' => $this->createDeleteForm($id)->createView(),
            ));
        }
    }

    /**
     * Creates a form to delete a Role entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('sam_role_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
