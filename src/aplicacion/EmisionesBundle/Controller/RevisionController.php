<?php

namespace aplicacion\EmisionesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use aplicacion\EmisionesBundle\Entity\Revision;
use aplicacion\EmisionesBundle\Form\RevisionType;

/**
 * Revision controller.
 *
 */
class RevisionController extends Controller
{

    /**
     * Lists all Revision entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('EmisionesBundle:Revision')->findAll();

        return $this->render('EmisionesBundle:Revision:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Revision entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Revision();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('revision_show', array('id' => $entity->getId())));
        }

        return $this->render('EmisionesBundle:Revision:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Revision entity.
     *
     * @param Revision $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Revision $entity)
    {
        $form = $this->createForm(new RevisionType(), $entity, array(
            'action' => $this->generateUrl('revision_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Revision entity.
     *
     */
    public function newAction()
    {
        $entity = new Revision();
        $form   = $this->createCreateForm($entity);

        return $this->render('EmisionesBundle:Revision:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Revision entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Revision')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Revision entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EmisionesBundle:Revision:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Revision entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Revision')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Revision entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EmisionesBundle:Revision:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Revision entity.
    *
    * @param Revision $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Revision $entity)
    {
        $form = $this->createForm(new RevisionType(), $entity, array(
            'action' => $this->generateUrl('revision_update', array('id' => $entity->getId())),
            //'method' => 'PUT',
        ));

       // $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Revision entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Revision')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Revision entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('revision_edit', array('id' => $id)));
        }

        return $this->render('EmisionesBundle:Revision:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Revision entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('EmisionesBundle:Revision')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Revision entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('revision'));
    }

    /**
     * Creates a form to delete a Revision entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('revision_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
