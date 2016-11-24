<?php

namespace aplicacion\EmisionesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use aplicacion\EmisionesBundle\Entity\Tarjetacredito;
use aplicacion\EmisionesBundle\Form\TarjetacreditoType;

/**
 * Tarjetacredito controller.
 *
 */
class TarjetacreditoController extends Controller
{

    /**
     * Lists all Tarjetacredito entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('EmisionesBundle:Tarjetacredito')->findAll();

        return $this->render('EmisionesBundle:Tarjetacredito:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Tarjetacredito entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Tarjetacredito();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tarjetacredito_show', array('id' => $entity->getId())));
        }

        return $this->render('EmisionesBundle:Tarjetacredito:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Tarjetacredito entity.
     *
     * @param Tarjetacredito $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Tarjetacredito $entity)
    {
        $form = $this->createForm(new TarjetacreditoType(), $entity, array(
            'action' => $this->generateUrl('tarjetacredito_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Tarjetacredito entity.
     *
     */
    public function newAction()
    {
        $entity = new Tarjetacredito();
        $form   = $this->createCreateForm($entity);

        return $this->render('EmisionesBundle:Tarjetacredito:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Tarjetacredito entity.
     *
     */
    public function showAction($id,$orden)
    {
        $em = $this->getDoctrine()->getManager();
        $orden=$em->getRepository('EmisionesBundle:Orden')->find($orden);
        $entity = $em->getRepository('EmisionesBundle:Tarjetacredito')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tarjetacredito entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EmisionesBundle:Tarjetacredito:show.html.twig', array(
            'fp'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'entity'=>$orden
        ));
    }

    /**
     * Displays a form to edit an existing Tarjetacredito entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Tarjetacredito')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tarjetacredito entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EmisionesBundle:Tarjetacredito:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Tarjetacredito entity.
    *
    * @param Tarjetacredito $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Tarjetacredito $entity)
    {
        $form = $this->createForm(new TarjetacreditoType(), $entity, array(
            'action' => $this->generateUrl('tarjetacredito_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Tarjetacredito entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Tarjetacredito')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tarjetacredito entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('tarjetacredito_edit', array('id' => $id)));
        }

        return $this->render('EmisionesBundle:Tarjetacredito:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Tarjetacredito entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('EmisionesBundle:Tarjetacredito')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Tarjetacredito entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('tarjetacredito'));
    }

    /**
     * Creates a form to delete a Tarjetacredito entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tarjetacredito_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
