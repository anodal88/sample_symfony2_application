<?php

namespace aplicacion\EmisionesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use aplicacion\EmisionesBundle\Entity\Continente;
use aplicacion\EmisionesBundle\Form\ContinenteType;

/**
 * Continente controller.
 *
 */
class ContinenteController extends Controller
{

    /**
     * Lists all Continente entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('EmisionesBundle:Continente')->findAll();

        return $this->render('EmisionesBundle:Continente:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Continente entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Continente();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('continente_show', array('id' => $entity->getId())));
        }

        return $this->render('EmisionesBundle:Continente:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Continente entity.
     *
     * @param Continente $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Continente $entity)
    {
        $form = $this->createForm(new ContinenteType(), $entity, array(
            'action' => $this->generateUrl('continente_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Continente entity.
     *
     */
    public function newAction()
    {
        $entity = new Continente();
        $form   = $this->createCreateForm($entity);

        return $this->render('EmisionesBundle:Continente:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Continente entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Continente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Continente entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EmisionesBundle:Continente:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Continente entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Continente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Continente entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EmisionesBundle:Continente:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Continente entity.
    *
    * @param Continente $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Continente $entity)
    {
        $form = $this->createForm(new ContinenteType(), $entity, array(
            'action' => $this->generateUrl('continente_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Continente entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Continente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Continente entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('continente_edit', array('id' => $id)));
        }

        return $this->render('EmisionesBundle:Continente:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Continente entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('EmisionesBundle:Continente')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Continente entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('continente'));
    }

    /**
     * Creates a form to delete a Continente entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('continente_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
