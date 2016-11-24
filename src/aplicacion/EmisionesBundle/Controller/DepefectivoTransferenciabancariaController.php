<?php

namespace aplicacion\EmisionesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use aplicacion\EmisionesBundle\Entity\DepefectivoTransferenciabancaria;
use aplicacion\EmisionesBundle\Form\DepefectivoTransferenciabancariaType;

/**
 * DepefectivoTransferenciabancaria controller.
 *
 */
class DepefectivoTransferenciabancariaController extends Controller
{

    /**
     * Lists all DepefectivoTransferenciabancaria entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('EmisionesBundle:DepefectivoTransferenciabancaria')->findAll();

        return $this->render('EmisionesBundle:DepefectivoTransferenciabancaria:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new DepefectivoTransferenciabancaria entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new DepefectivoTransferenciabancaria();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('depefectivotransferenciabancaria_show', array('id' => $entity->getId())));
        }

        return $this->render('EmisionesBundle:DepefectivoTransferenciabancaria:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a DepefectivoTransferenciabancaria entity.
     *
     * @param DepefectivoTransferenciabancaria $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(DepefectivoTransferenciabancaria $entity)
    {
        $form = $this->createForm(new DepefectivoTransferenciabancariaType(), $entity, array(
            'action' => $this->generateUrl('depefectivotransferenciabancaria_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new DepefectivoTransferenciabancaria entity.
     *
     */
    public function newAction()
    {
        $entity = new DepefectivoTransferenciabancaria();
        $form   = $this->createCreateForm($entity);

        return $this->render('EmisionesBundle:DepefectivoTransferenciabancaria:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a DepefectivoTransferenciabancaria entity.
     *
     */
    public function showAction($id,$orden)
    {
        $em = $this->getDoctrine()->getManager();
        $orden=$em->getRepository('EmisionesBundle:Orden')->find($orden);
        $entity = $em->getRepository('EmisionesBundle:DepefectivoTransferenciabancaria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DepefectivoTransferenciabancaria entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EmisionesBundle:DepefectivoTransferenciabancaria:show.html.twig', array(
            'entity'      => $orden,
            'delete_form' => $deleteForm->createView(),
            'fp'=>$entity
        ));
    }

    /**
     * Displays a form to edit an existing DepefectivoTransferenciabancaria entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:DepefectivoTransferenciabancaria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DepefectivoTransferenciabancaria entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EmisionesBundle:DepefectivoTransferenciabancaria:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a DepefectivoTransferenciabancaria entity.
    *
    * @param DepefectivoTransferenciabancaria $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(DepefectivoTransferenciabancaria $entity)
    {
        $form = $this->createForm(new DepefectivoTransferenciabancariaType(), $entity, array(
            'action' => $this->generateUrl('depefectivotransferenciabancaria_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing DepefectivoTransferenciabancaria entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:DepefectivoTransferenciabancaria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DepefectivoTransferenciabancaria entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('depefectivotransferenciabancaria_edit', array('id' => $id)));
        }

        return $this->render('EmisionesBundle:DepefectivoTransferenciabancaria:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a DepefectivoTransferenciabancaria entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('EmisionesBundle:DepefectivoTransferenciabancaria')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find DepefectivoTransferenciabancaria entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('depefectivotransferenciabancaria'));
    }

    /**
     * Creates a form to delete a DepefectivoTransferenciabancaria entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('depefectivotransferenciabancaria_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
