<?php

namespace aplicacion\EmisionesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use aplicacion\EmisionesBundle\Entity\Aerolinea;
use aplicacion\EmisionesBundle\Form\AerolineaType;

/**
 * Aerolinea controller.
 *
 */
class AerolineaController extends Controller
{

    /**
     * Lists all Aerolinea entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('EmisionesBundle:Aerolinea')->findAll();

        return $this->render('EmisionesBundle:Aerolinea:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Aerolinea entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Aerolinea();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('aerolinea_show', array('id' => $entity->getId())));
        }

        return $this->render('EmisionesBundle:Aerolinea:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Aerolinea entity.
     *
     * @param Aerolinea $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Aerolinea $entity)
    {
        $form = $this->createForm(new AerolineaType(), $entity, array(
            'action' => $this->generateUrl('aerolinea_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Aerolinea entity.
     *
     */
    public function newAction()
    {
        $entity = new Aerolinea();
        $form   = $this->createCreateForm($entity);

        return $this->render('EmisionesBundle:Aerolinea:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Aerolinea entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Aerolinea')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Aerolinea entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EmisionesBundle:Aerolinea:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Aerolinea entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Aerolinea')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Aerolinea entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EmisionesBundle:Aerolinea:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Aerolinea entity.
    *
    * @param Aerolinea $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Aerolinea $entity)
    {
        $form = $this->createForm(new AerolineaType(), $entity, array(
            'action' => $this->generateUrl('aerolinea_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Aerolinea entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Aerolinea')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Aerolinea entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('aerolinea_edit', array('id' => $id)));
        }

        return $this->render('EmisionesBundle:Aerolinea:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Aerolinea entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('EmisionesBundle:Aerolinea')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Aerolinea entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('aerolinea'));
    }

    /**
     * Creates a form to delete a Aerolinea entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('aerolinea_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
