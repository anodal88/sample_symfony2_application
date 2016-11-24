<?php

namespace aplicacion\EmisionesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use aplicacion\EmisionesBundle\Entity\Orden;
use aplicacion\EmisionesBundle\Form\OrdenType;

/**
 * Orden controller.
 *
 */
class OrdenController extends Controller
{

    /**
     * Lists all Orden entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('EmisionesBundle:Orden')->findAll();

        return $this->render('EmisionesBundle:Orden:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Orden entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Orden();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('orden_show', array('id' => $entity->getId())));
        }

        return $this->render('EmisionesBundle:Orden:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Orden entity.
     *
     * @param Orden $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Orden $entity)
    {
        $form = $this->createForm(new OrdenType(), $entity, array(
            'action' => $this->generateUrl('orden_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Orden entity.
     *
     */
    public function newAction()
    {
        $entity = new Orden();
        $form   = $this->createCreateForm($entity);

        return $this->render('EmisionesBundle:Orden:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Orden entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Orden')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Orden entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EmisionesBundle:Orden:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Orden entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Orden')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Orden entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EmisionesBundle:Orden:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Orden entity.
    *
    * @param Orden $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Orden $entity)
    {
        $form = $this->createForm(new OrdenType(), $entity, array(
            'action' => $this->generateUrl('orden_update', array('id' => $entity->getId())),
            //'method' => 'PUT',
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Orden entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Orden')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Orden entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            print_r('valido2');exit;
            $em->flush();

            return $this->redirect($this->generateUrl('orden_edit', array('id' => $id)));
        }

        return $this->render('EmisionesBundle:Orden:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Orden entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('EmisionesBundle:Orden')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Orden entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('orden'));
    }

    /**
     * Creates a form to delete a Orden entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('orden_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
