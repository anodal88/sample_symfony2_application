<?php

namespace aplicacion\EmisionesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use aplicacion\EmisionesBundle\Entity\Pagodirecto;
use aplicacion\EmisionesBundle\Form\PagodirectoType;

/**
 * Pagodirecto controller.
 *
 */
class PagodirectoController extends Controller
{

    /**
     * Lists all Pagodirecto entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('EmisionesBundle:Pagodirecto')->findAll();

        return $this->render('EmisionesBundle:Pagodirecto:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Pagodirecto entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Pagodirecto();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('pagodirecto_show', array('id' => $entity->getId())));
        }

        return $this->render('EmisionesBundle:Pagodirecto:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Pagodirecto entity.
     *
     * @param Pagodirecto $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Pagodirecto $entity)
    {
        $form = $this->createForm(new PagodirectoType(), $entity, array(
            'action' => $this->generateUrl('pagodirecto_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Pagodirecto entity.
     *
     */
    public function newAction()
    {
        $entity = new Pagodirecto();
        $form   = $this->createCreateForm($entity);

        return $this->render('EmisionesBundle:Pagodirecto:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Pagodirecto entity.
     *
     */
    public function showAction($id,$orden)
    {
        $em = $this->getDoctrine()->getManager();
        $orden=$em->getRepository('EmisionesBundle:Orden')->find($orden);
        $entity = $em->getRepository('EmisionesBundle:Pagodirecto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pagodirecto entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EmisionesBundle:Pagodirecto:show.html.twig', array(
            'entity'      => $orden,
            'delete_form' => $deleteForm->createView(),
            'fp'=>$entity
        ));
    }

    /**
     * Displays a form to edit an existing Pagodirecto entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Pagodirecto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pagodirecto entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EmisionesBundle:Pagodirecto:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Pagodirecto entity.
    *
    * @param Pagodirecto $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Pagodirecto $entity)
    {
        $form = $this->createForm(new PagodirectoType(), $entity, array(
            'action' => $this->generateUrl('pagodirecto_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Pagodirecto entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Pagodirecto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pagodirecto entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('pagodirecto_edit', array('id' => $id)));
        }

        return $this->render('EmisionesBundle:Pagodirecto:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Pagodirecto entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('EmisionesBundle:Pagodirecto')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Pagodirecto entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('pagodirecto'));
    }

    /**
     * Creates a form to delete a Pagodirecto entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pagodirecto_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
