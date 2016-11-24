<?php

namespace aplicacion\EmisionesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use aplicacion\EmisionesBundle\Entity\Anulacion;
use aplicacion\EmisionesBundle\Form\AnulacionType;

/**
 * Anulacion controller.
 *
 */
class AnulacionController extends Controller
{

    /**
     * Lists all Anulacion entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('EmisionesBundle:Anulacion')->findAll();

        return $this->render('EmisionesBundle:Anulacion:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Anulacion entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Anulacion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('anulacion_show', array('id' => $entity->getId())));
        }

        return $this->render('EmisionesBundle:Anulacion:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Anulacion entity.
     *
     * @param Anulacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Anulacion $entity)
    {
        $form = $this->createForm(new AnulacionType(), $entity, array(
            'action' => $this->generateUrl('anulacion_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Anulacion entity.
     *
     */
    public function newAction()
    {
        $entity = new Anulacion();
        $form   = $this->createCreateForm($entity);

        return $this->render('EmisionesBundle:Anulacion:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Anulacion entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Anulacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Anulacion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EmisionesBundle:Anulacion:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Anulacion entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Anulacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Anulacion entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EmisionesBundle:Anulacion:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Anulacion entity.
    *
    * @param Anulacion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Anulacion $entity)
    {
        $form = $this->createForm(new AnulacionType(), $entity, array(
            'action' => $this->generateUrl('anulacion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Anulacion entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Anulacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Anulacion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('anulacion_edit', array('id' => $id)));
        }

        return $this->render('EmisionesBundle:Anulacion:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Anulacion entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('EmisionesBundle:Anulacion')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Anulacion entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('anulacion'));
    }

    /**
     * Creates a form to delete a Anulacion entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('anulacion_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
