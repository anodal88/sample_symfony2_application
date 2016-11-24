<?php

namespace aplicacion\EmisionesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\UserBundle\Controller\RegistrationController;
use aplicacion\EmisionesBundle\Entity\Agente;
use aplicacion\EmisionesBundle\Form\AgenteType;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;

/**
 * Agente controller.
 *
 */
class AgenteController extends RegistrationController
{

    public function  vistaAgenteAction()
    {       
        return $this->render('EmisionesBundle:Agente:vistaAgente.html.twig', array(
            //'entities' => $entities,
        ));
    }
    
    public function registerAction(Request $request)
    {
        $allowed = array('png', 'jpg', 'gif','jpeg');
        $path = $this->container->getParameter('aplicacion.directorio.imagenes.usuarios');
        $em = $this->getDoctrine()->getManager();
            
//        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
//        $formFactory = $this->get('aplicacion_emisionesbundle.agente.form.type');
//        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
//        $userManager = $this->get('fos_user.user_manager');
//        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');
        $entity = new Agente();
        $entity->setEnabled(true);
        //$user = $userManager->createUser();
        //$user->setEnabled(true);

        $event = new GetResponseUserEvent($entity, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);
        
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }
        
       
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
//        $form = $formFactory->createForm();
//        $form->setData($user);

       
        if ($form->isValid()) {  
            $event = new FormEvent($form, $request);            
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
            
                 /*Agregar la foto a la carpeta de imagenes*/
        if(isset($_FILES['aplicacion_emisionesbundle_agente']['name']['foto']) && $_FILES['aplicacion_emisionesbundle_agente']['error']['foto'] == 0)
            {

                $extension = pathinfo($_FILES['aplicacion_emisionesbundle_agente']['name']['foto'], PATHINFO_EXTENSION);
                //print_r($extension);exit;
                $foto=  $this->crearNombre(10,$extension);
                if(!in_array(strtolower($extension), $allowed)){
                       $this->get('session')->getFlashBag()->add('error', 'El fichero que usted adjunta no es permitido!');
                       
                }

                if(move_uploaded_file($_FILES['aplicacion_emisionesbundle_agente']['tmp_name']['foto'], $path.$foto)){
                        $entity->setFoto($foto);
                }
            }

            $entity->addRole('ROLE_AGENTE_EXTERNO');
            $em->persist($entity);
            $em->flush();
            
            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_registration_confirmed');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($entity, $request, $response));

            return $response;
        }

        return $this->render('EmisionesBundle:Agente:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    function crearNombre($length,$ext)
    {
        if( ! isset($length) or ! is_numeric($length) ) $length=6;

        $str  = "0123456789abcdefghijklmnopqrstuvwxyz";
        $path = '';

        for($i=1 ; $i<$length ; $i++)
          $path .= $str{rand(0,strlen($str)-1)};

        return $path.'_'.date("d-m-Y_H-i-s").'.'.$ext;    
    }
    
    /**
     * Lists all Agente entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('EmisionesBundle:Agente')->findAll();

        return $this->render('EmisionesBundle:Agente:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Agente entity.
     *
     */
    public function createAction(Request $request)
    {
        
        $entity = new Agente();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('agente_show', array('id' => $entity->getId())));
        }

        return $this->render('EmisionesBundle:Agente:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Agente entity.
     *
     * @param Agente $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Agente $entity)
    {
        $form = $this->createForm(new AgenteType(), $entity, array(
            'action' => $this->generateUrl('agente_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Agente entity.
     *
     */
    public function newAction()
    {
        $entity = new Agente();
        $form   = $this->createCreateForm($entity);

        return $this->render('EmisionesBundle:Agente:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Agente entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Agente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Agente entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EmisionesBundle:Agente:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Agente entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Agente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Agente entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EmisionesBundle:Agente:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Agente entity.
    *
    * @param Agente $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Agente $entity)
    {
        $form = $this->createForm(new AgenteType(), $entity, array(
            'action' => $this->generateUrl('agente_update', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Agente entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $allowed = array('png', 'jpg', 'gif','jpeg');
        $path = $this->container->getParameter('aplicacion.directorio.imagenes.usuarios');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EmisionesBundle:Agente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Agente entity.');
        }
        $avatarold=$entity->getFoto();//respaldando el avatar antiguo
        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setPassword($editForm->getData()->getPlainPassword());
            /*Agregar la foto a la carpeta de imagenes*/
        if(isset($_FILES['aplicacion_emisionesbundle_agente']['name']['foto']) && $_FILES['aplicacion_emisionesbundle_agente']['error']['foto'] == 0)
            {
            
                $extension = pathinfo($_FILES['aplicacion_emisionesbundle_agente']['name']['foto'], PATHINFO_EXTENSION);
                //print_r($extension);exit;
                $foto=  $this->crearNombre(10,$extension);
                if(!in_array(strtolower($extension), $allowed)){
                       $this->get('session')->getFlashBag()->add('error', 'El fichero que usted adjunta no es permitido!');
                       
                }
                if($avatarold!='')
                {   /*Eliminando el avatar viejo*/
                    
                    if(is_file($path.$avatarold))
                    {
                        unlink($path.$avatarold);
                    }
                }

                if(move_uploaded_file($_FILES['aplicacion_emisionesbundle_agente']['tmp_name']['foto'], $path.$foto)){
                        $entity->setFoto($foto);
                }
            
            }
            else
            {
                $entity->setFoto($avatarold);
            }
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', "Su perfil ha sido actualizado!");
            return $this->redirect($this->generateUrl('agente_edit', array('id' => $id)));
        }
        else
        {
            $entity->setFoto($avatarold);
           $this->get('session')->getFlashBag()->add('error', "Ha ocurrido un error, por favor revisar detalladamente los valores porporcionados!"); 
        }

        return $this->render('EmisionesBundle:Agente:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Agente entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('EmisionesBundle:Agente')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Agente entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('agente'));
    }

    /**
     * Creates a form to delete a Agente entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('agente_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
